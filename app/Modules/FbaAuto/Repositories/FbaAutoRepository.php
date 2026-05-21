<?php

namespace App\Modules\FbaAuto\Repositories;

use App\Modules\FbaAuto\Models\FbaAuto;
use App\Modules\FbaAuto\Models\FbaState;
use App\Modules\FbaAuto\Models\FbaWarehouse;
use App\Models\ProductName;
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
        if (Schema::hasTable('fba_warehouses')) {
            $warehouses = FbaWarehouse::active()->ordered()->pluck('name')->toArray();
            if (!empty($warehouses)) {
                return $warehouses;
            }
        }

        return $this->model->whereNotNull('warehouse_name')
            ->distinct()
            ->orderBy('warehouse_name')
            ->pluck('warehouse_name')
            ->toArray();
    }

    public function syncWarehouse(string $name): void
    {
        $name = $this->cleanSelectValue($name);
        if ($name === '' || ! Schema::hasTable('fba_warehouses')) return;

        FbaWarehouse::firstOrCreate(
            ['name' => $name],
            ['is_active' => true, 'sort_order' => 0]
        );
    }

    public function syncState(string $name): void
    {
        $name = $this->cleanSelectValue($name);
        if ($name === '' || ! Schema::hasTable('fba_states')) return;

        $state = FbaState::firstOrNew(['name' => $name]);

        if (! $state->exists) {
            $state->code = $this->uniqueStateCode($name);
            $state->is_active = true;
            $state->sort_order = 0;
            $state->save();
            return;
        }

        if ($state->code === null || trim((string) $state->code) === '') {
            $state->code = $this->uniqueStateCode($name, $state->id);
            $state->save();
        }
    }

    public function syncProductName(string $name): void
    {
        $name = $this->cleanSelectValue($name);
        if ($name === '') return;

        ProductName::firstOrCreate(
            ['name' => $name],
            ['is_deleted' => 0, 'created_by' => auth()->id() ?? 1, 'modified_by' => auth()->id() ?? 1]
        );
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

    private function cleanSelectValue(string $value): string
    {
        return trim(preg_replace('/\s+/', ' ', $value));
    }

    private function uniqueStateCode(string $name, ?int $ignoreId = null): string
    {
        $base = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $name));
        $base = $base !== '' ? substr($base, 0, 10) : 'STATE';
        $code = $base;
        $suffix = 1;

        while ($this->stateCodeExists($code, $ignoreId)) {
            $suffixText = (string) $suffix++;
            $code = substr($base, 0, max(1, 10 - strlen($suffixText))) . $suffixText;
        }

        return $code;
    }

    private function stateCodeExists(string $code, ?int $ignoreId = null): bool
    {
        return FbaState::query()
            ->where('code', $code)
            ->when($ignoreId, fn($query) => $query->where('id', '!=', $ignoreId))
            ->exists();
    }
}
