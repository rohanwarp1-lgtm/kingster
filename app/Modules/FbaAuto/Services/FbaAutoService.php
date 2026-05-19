<?php

namespace App\Modules\FbaAuto\Services;

use App\Modules\FbaAuto\Models\FbaAuto;
use App\Modules\FbaAuto\Repositories\FbaAutoRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class FbaAutoService
{
    public function __construct(
        private FbaAutoRepository $repository
    ) {}

    public function createShipment(array $data): FbaAuto
    {
        return DB::transaction(function () use ($data) {
            $data['shipment_id'] = 'FBA-' . strtoupper(Str::random(8));
            $data['generated_by'] = auth()->id();
            $data['status'] = 'pending';

            $shipment = $this->repository->create($data);

            activity()
                ->performedOn($shipment)
                ->causedBy(auth()->user())
                ->withProperties([
                    'shipment_id' => $shipment->shipment_id,
                    'product_name' => $shipment->product_name,
                    'qty' => $shipment->qty,
                    'warehouse' => $shipment->warehouse_name,
                ])
                ->log('FBA Shipment created');

            return $shipment;
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

            $updated = $this->repository->update($id, $data);

            if ($updated) {
                $shipment = $this->repository->find($id);
                
                activity()
                    ->performedOn($shipment)
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'changes' => $shipment->getChanges(),
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
}
