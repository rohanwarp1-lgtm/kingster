<?php

namespace App\Modules\FbaAuto\Repositories;

use App\Modules\FbaAuto\Models\FbaAuto;
use App\Modules\FbaAuto\Models\FbaState;
use App\Modules\FbaAuto\Interfaces\FbaAutoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class FbaAutoRepository implements FbaAutoRepositoryInterface
{
    public function __construct(
        private FbaAuto $model
    ) {}

    public function all(array $filters = []): Collection|LengthAwarePaginator
    {
        $query = $this->model->with(['generator', 'updater'])
            ->filter($filters)
            ->orderBy('created_at', 'desc');

        if (isset($filters['paginate']) && $filters['paginate']) {
            return $query->paginate($filters['paginate'] ?? 10);
        }

        return $query->get();
    }

    public function find(int $id): ?FbaAuto
    {
        return $this->model->with(['generator', 'updater'])->find($id);
    }

    public function create(array $data): FbaAuto
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $record = $this->find($id);
        if (!$record) {
            return false;
        }
        return $record->update($data);
    }

    public function delete(int $id): bool
    {
        $record = $this->find($id);
        if (!$record) {
            return false;
        }
        return $record->delete();
    }

    public function restore(int $id): bool
    {
        $record = $this->model->withTrashed()->find($id);
        if (!$record || !$record->trashed()) {
            return false;
        }
        return $record->restore();
    }

    public function forceDelete(int $id): bool
    {
        $record = $this->model->withTrashed()->find($id);
        if (!$record) {
            return false;
        }
        return $record->forceDelete();
    }

    public function findByShipmentId(string $shipmentId): ?FbaAuto
    {
        return $this->model->where('shipment_id', $shipmentId)->first();
    }

    public function getByShipmentId(string $shipmentId): Collection
    {
        return $this->model->where('shipment_id', $shipmentId)
            ->with(['generator', 'updater'])
            ->orderBy('id')
            ->get();
    }

    public function getProductNames(): array
    {
        return $this->model->whereNotNull('product_name')
            ->distinct()
            ->orderBy('product_name')
            ->pluck('product_name')
            ->toArray();
    }

    public function searchProducts(string $term): array
    {
        return $this->model->newQuery()
            ->select('product_name')
            ->whereNotNull('product_name')
            ->when($term !== '', fn($q) => $q->where('product_name', 'like', "%{$term}%"))
            ->distinct()
            ->orderBy('product_name')
            ->limit(50)
            ->pluck('product_name')
            ->toArray();
    }

    public function getWarehouses(): array
    {
        return $this->model->whereNotNull('warehouse_name')
            ->distinct()
            ->pluck('warehouse_name')
            ->toArray();
    }

    public function getStates(): array
    {
        if (Schema::hasTable('fba_states')) {
            $states = FbaState::query()
                ->active()
                ->ordered()
                ->pluck('name')
                ->toArray();

            if (!empty($states)) {
                return $states;
            }
        }

        return $this->model->whereNotNull('state')
            ->distinct()
            ->orderBy('state')
            ->pluck('state')
            ->toArray();
    }
}
