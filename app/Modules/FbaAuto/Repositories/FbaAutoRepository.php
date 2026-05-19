<?php

namespace App\Modules\FbaAuto\Repositories;

use App\Modules\FbaAuto\Models\FbaAuto;
use App\Modules\FbaAuto\Interfaces\FbaAutoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

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

    public function getWarehouses(): array
    {
        return $this->model->whereNotNull('warehouse_name')
            ->distinct()
            ->pluck('warehouse_name')
            ->toArray();
    }

    public function getStates(): array
    {
        return $this->model->whereNotNull('state')
            ->distinct()
            ->pluck('state')
            ->toArray();
    }
}
