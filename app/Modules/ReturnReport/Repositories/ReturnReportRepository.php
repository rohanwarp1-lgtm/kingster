<?php

namespace App\Modules\ReturnReport\Repositories;

use App\Modules\ReturnReport\Models\ReturnReport;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ReturnReportRepository
{
    public function all(array $filters = []): Collection|LengthAwarePaginator
    {
        $query = ReturnReport::filter($filters)
            ->orderBy('created_at', 'desc');

        if (isset($filters['paginate']) && $filters['paginate']) {
            return $query->paginate($filters['paginate'] ?? 10);
        }

        return $query->get();
    }

    public function find(int $id): ?ReturnReport
    {
        return ReturnReport::find($id);
    }

    public function create(array $data): ReturnReport
    {
        return ReturnReport::create($data);
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

    public function getWarehouses(): array
    {
        return ReturnReport::whereNotNull('warehouse')
            ->distinct()
            ->pluck('warehouse')
            ->toArray();
    }

    public function getReturnReasons(): array
    {
        return ReturnReport::whereNotNull('return_reason')
            ->distinct()
            ->pluck('return_reason')
            ->toArray();
    }

    public function getMarketplaces(): array
    {
        return ReturnReport::whereNotNull('marketplace')
            ->distinct()
            ->pluck('marketplace')
            ->toArray();
    }

    public function getByDateRange(string $startDate, string $endDate, string $groupBy = 'day'): Collection
    {
        return ReturnReport::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("DATE(created_at) as date, COUNT(*) as returns, SUM(loss_amount) as total_loss")
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function getMarketplaceStats(string $startDate, string $endDate): Collection
    {
        return ReturnReport::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('marketplace, COUNT(*) as total_returns, SUM(loss_amount) as total_loss, AVG(loss_amount) as avg_loss')
            ->groupBy('marketplace')
            ->get();
    }

    public function getReasonStats(string $startDate, string $endDate): Collection
    {
        return ReturnReport::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('return_reason, COUNT(*) as count, SUM(loss_amount) as total_loss')
            ->groupBy('return_reason')
            ->orderByDesc('count')
            ->get();
    }

    public function getWarehouseLossStats(string $startDate, string $endDate): Collection
    {
        return ReturnReport::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('warehouse, COUNT(*) as returns, SUM(loss_amount) as total_loss, AVG(loss_amount) as avg_loss')
            ->groupBy('warehouse')
            ->orderByDesc('total_loss')
            ->get();
    }
}
