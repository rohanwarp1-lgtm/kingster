<?php

namespace App\Modules\ReturnReport\Services;

use App\Modules\ReturnReport\Repositories\ReturnReportRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AnalyticsService
{
    public function __construct(
        private ReturnReportRepository $repository
    ) {}

    public function getDashboardStats(?string $startDate = null, ?string $endDate = null): array
    {
        $startDate = $startDate ?? now()->startOfMonth()->toDateString();
        $endDate = $endDate ?? now()->endOfMonth()->toDateString();

        $totalReturns = $this->repository->all([
            'date_from' => $startDate,
            'date_to' => $endDate,
        ])->count();

        $totalLoss = $this->repository->all([
            'date_from' => $startDate,
            'date_to' => $endDate,
        ])->sum('loss_amount');

        $totalReturnCost = $this->repository->all([
            'date_from' => $startDate,
            'date_to' => $endDate,
        ])->sum('return_cost');

        $avgLossPerReturn = $totalReturns > 0 ? $totalLoss / $totalReturns : 0;

        $byMarketplace = $this->repository->getMarketplaceStats($startDate, $endDate);
        $byReason = $this->repository->getReasonStats($startDate, $endDate);
        $byWarehouse = $this->repository->getWarehouseLossStats($startDate, $endDate);
        $dailyTrend = $this->repository->getByDateRange($startDate, $endDate);

        return [
            'total_returns' => $totalReturns,
            'total_loss' => round($totalLoss, 2),
            'total_return_cost' => round($totalReturnCost, 2),
            'avg_loss_per_return' => round($avgLossPerReturn, 2),
            'by_marketplace' => $byMarketplace,
            'by_reason' => $byReason,
            'by_warehouse' => $byWarehouse,
            'daily_trend' => $dailyTrend,
        ];
    }

    public function getReport(string $type, array $filters): Collection
    {
        $startDate = $filters['date_from'] ?? now()->startOfMonth()->toDateString();
        $endDate = $filters['date_to'] ?? now()->endOfMonth()->toDateString();

        return match($type) {
            'marketplace' => $this->repository->getMarketplaceStats($startDate, $endDate),
            'reason' => $this->repository->getReasonStats($startDate, $endDate),
            'warehouse' => $this->repository->getWarehouseLossStats($startDate, $endDate),
            'daily' => $this->repository->getByDateRange($startDate, $endDate),
            default => collect([]),
        };
    }

    public function getTopReturnReasons(int $limit = 10): Collection
    {
        return $this->repository->getReturnReasons()
            ->map(function ($reason) {
                return ReturnReport::where('return_reason', $reason)
                    ->selectRaw('return_reason, COUNT(*) as count, SUM(loss_amount) as total_loss')
                    ->groupBy('return_reason')
                    ->first();
            })
            ->filter()
            ->sortByDesc('count')
            ->take($limit);
    }

    public function getKpiMetrics(?string $startDate = null, ?string $endDate = null): array
    {
        $startDate = $startDate ?? now()->startOfMonth()->toDateString();
        $endDate = $endDate ?? now()->endOfMonth()->toDateString();

        $currentPeriod = $this->repository->all([
            'date_from' => $startDate,
            'date_to' => $endDate,
        ]);

        $previousStartDate = Carbon::parse($startDate)->subMonth()->startOfMonth()->toDateString();
        $previousEndDate = Carbon::parse($endDate)->subMonth()->endOfMonth()->toDateString();
        $previousPeriod = $this->repository->all([
            'date_from' => $previousStartDate,
            'date_to' => $previousEndDate,
        ]);

        $currentReturns = $currentPeriod->count();
        $previousReturns = $previousPeriod->count();
        $returnChange = $previousReturns > 0 
            ? round((($currentReturns - $previousReturns) / $previousReturns) * 100, 2) 
            : 0;

        $currentLoss = $currentPeriod->sum('loss_amount');
        $previousLoss = $previousPeriod->sum('loss_amount');
        $lossChange = $previousLoss > 0 
            ? round((($currentLoss - $previousLoss) / $previousLoss) * 100, 2) 
            : 0;

        return [
            'total_returns' => $currentReturns,
            'total_returns_change' => $returnChange,
            'total_loss' => round($currentLoss, 2),
            'total_loss_change' => $lossChange,
            'avg_loss_per_return' => $currentReturns > 0 ? round($currentLoss / $currentReturns, 2) : 0,
            'return_rate' => $this->calculateReturnRate($currentPeriod->count()),
        ];
    }

    protected function calculateReturnRate(int $returns): float
    {
        $totalOrders = 1000;
        return round(($returns / $totalOrders) * 100, 2);
    }
}
