<?php

namespace App\Modules\FbaAuto\Services;

use App\Modules\FbaAuto\Repositories\FbaAutoRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class FbaAutoService
{
    public function __construct(
        private FbaAutoRepository $repository
    ) {}

    public function createShipment(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $header = [
                'shipment_id'    => $data['shipment_id'],
                'shipment_date'  => $data['shipment_date'],
                'state'          => $data['state'],
                'warehouse_name' => $data['warehouse_name'],
                'generated_by'   => auth()->id(),
                'status'         => 'pending',
            ];

            $shipments = [];
            foreach ($data['items'] as $item) {
                $shipments[] = $this->repository->create(array_merge($header, [
                    'product_name' => $item['product_name'],
                    'qty'          => $item['qty'],
                    'qty_price'    => $item['qty_price'],
                ]));
            }

            activity()
                ->causedBy(auth()->user())
                ->withProperties([
                    'shipment_id'   => $data['shipment_id'],
                    'item_count'    => count($shipments),
                    'warehouse'     => $data['warehouse_name'],
                ])
                ->log('FBA Shipment created');

            return $shipments;
        });
    }

    public function updateShipment(int $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            $data['updated_by'] = auth()->id();
            
            $oldShipment = $this->repository->find($id);
            
            if (!$oldShipment) {
                throw new Exception('Shipment not found');
            }

            $trackedFields = [
                'shipment_date',
                'product_name',
                'qty',
                'state',
                'warehouse_name',
                'qty_price',
                'status',
            ];
            $before = $oldShipment->only($trackedFields);

            $updated = $this->repository->update($id, $data);

            if ($updated) {
                $shipment = $this->repository->find($id);
                $after = $shipment->only($trackedFields);
                $changes = [];

                foreach ($trackedFields as $field) {
                    $oldValue = $before[$field] ?? null;
                    $newValue = $after[$field] ?? null;

                    if ((string) $oldValue !== (string) $newValue) {
                        $changes[$field] = [
                            'old' => $oldValue,
                            'new' => $newValue,
                        ];
                    }
                }
                
                activity()
                    ->performedOn($shipment)
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'changes' => $changes,
                    ])
                    ->log('FBA Shipment updated');
            }

            return $updated;
        });
    }

    public function updateStatus(int $id, string $newStatus, ?string $notes = null): bool
    {
        return DB::transaction(function () use ($id, $newStatus, $notes) {
            $shipment = $this->repository->find($id);
            
            if (!$shipment) {
                throw new Exception('Shipment not found');
            }

            $oldStatus = $shipment->status;

            if (!$this->isValidTransition($oldStatus, $newStatus)) {
                throw new Exception("Invalid status transition from '{$oldStatus}' to '{$newStatus}'");
            }

            $updated = $this->repository->update($id, [
                'status' => $newStatus,
                'updated_by' => auth()->id(),
            ]);

            if ($updated) {
                $shipment->refresh();

                activity()
                    ->performedOn($shipment)
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus,
                        'notes' => $notes,
                    ])
                    ->log("FBA Status changed from {$oldStatus} to {$newStatus}");
            }

            return $updated;
        });
    }

    public function deleteShipment(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $shipment = $this->repository->find($id);
            
            if (!$shipment) {
                throw new Exception('Shipment not found');
            }

            $deleted = $this->repository->delete($id);

            if ($deleted) {
                activity()
                    ->performedOn($shipment)
                    ->causedBy(auth()->user())
                    ->log('FBA Shipment deleted');
            }

            return $deleted;
        });
    }

    public function restoreShipment(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $shipment = $this->repository->find($id);
            
            if (!$shipment) {
                throw new Exception('Shipment not found');
            }

            $restored = $this->repository->restore($id);

            if ($restored) {
                activity()
                    ->performedOn($shipment)
                    ->causedBy(auth()->user())
                    ->log('FBA Shipment restored');
            }

            return $restored;
        });
    }

    public function bulkStatusUpdate(array $ids, string $newStatus): array
    {
        $results = ['success' => [], 'failed' => []];

        foreach ($ids as $id) {
            try {
                $this->updateStatus($id, $newStatus);
                $results['success'][] = $id;
            } catch (Exception $e) {
                $results['failed'][] = [
                    'id' => $id,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    public function getDashboardStats(): array
    {
        $total = $this->repository->all()->count();
        $pending = $this->repository->all(['status' => 'pending'])->count();
        $processing = $this->repository->all(['status' => 'processing'])->count();
        $delivered = $this->repository->all(['status' => 'delivered'])->count();

        return [
            'total' => $total,
            'pending' => $pending,
            'processing' => $processing,
            'delivered' => $delivered,
        ];
    }

    protected function isValidTransition(string $from, string $to): bool
    {
        $transitions = [
            'pending' => ['processing', 'cancelled'],
            'processing' => ['shipped', 'pending', 'cancelled'],
            'shipped' => ['delivered', 'returned'],
            'delivered' => ['closed'],
            'closed' => [],
            'cancelled' => [],
            'returned' => ['closed'],
        ];

        return in_array($to, $transitions[$from] ?? []);
    }

    public function getFilteredData(array $filters)
    {
        return $this->repository->all($filters);
    }

    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    public function getShipmentGroup(string $shipmentId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->getByShipmentId($shipmentId);
    }

    public function updateShipmentFull(string $shipmentId, array $data): bool
    {
        return DB::transaction(function () use ($shipmentId, $data) {
            $existing = $this->repository->getByShipmentId($shipmentId);

            if ($existing->isEmpty()) {
                throw new Exception('Shipment not found');
            }

            $first = $existing->first();
            $header = [
                'shipment_date'  => $data['shipment_date'],
                'state'          => $data['state'],
                'warehouse_name' => $data['warehouse_name'],
                'status'         => $data['status'] ?? $first->status,
                'updated_by'     => auth()->id(),
            ];

            $submittedIds = collect($data['items'])
                ->pluck('id')
                ->filter()
                ->map(fn($id) => (int) $id)
                ->values()
                ->toArray();

            // Soft-delete rows removed from the form
            $existing->each(function ($row) use ($submittedIds) {
                if (!in_array($row->id, $submittedIds)) {
                    $row->delete();
                }
            });

            // Update existing rows or create new ones
            foreach ($data['items'] as $item) {
                $itemData = array_merge($header, [
                    'product_name' => $item['product_name'],
                    'qty'          => $item['qty'],
                    'qty_price'    => $item['qty_price'],
                ]);

                if (!empty($item['id'])) {
                    $this->repository->update((int) $item['id'], $itemData);
                } else {
                    $this->repository->create(array_merge($itemData, [
                        'shipment_id'  => $shipmentId,
                        'generated_by' => $first->generated_by,
                    ]));
                }
            }

            activity()
                ->causedBy(auth()->user())
                ->withProperties(['shipment_id' => $shipmentId, 'item_count' => count($data['items'])])
                ->log('FBA Shipment updated');

            return true;
        });
    }

    public function searchProducts(string $term): array
    {
        return $this->repository->searchProducts($term);
    }

    public function getWarehouses(): array
    {
        return $this->repository->getWarehouses();
    }

    public function getStates(): array
    {
        return $this->repository->getStates();
    }

    public function getProductNames(): array
    {
        return $this->repository->getProductNames();
    }
}
