<?php

namespace App\Modules\ReturnReport\Services;

use App\Modules\ReturnReport\Repositories\ReturnReportRepository;
use App\Modules\ReturnReport\Exports\ReturnReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ExportService
{
    public function __construct(
        private ReturnReportRepository $repository
    ) {}

    public function exportToExcel(array $filters): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $data = $this->repository->all($filters);
        
        return Excel::download(
            new ReturnReportExport($data),
            'Return_Report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx'
        );
    }

    public function exportToCsv(array $filters): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $data = $this->repository->all($filters);
        
        return Excel::download(
            new ReturnReportExport($data),
            'Return_Report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv'
        );
    }

    public function generateReport(string $type, array $filters): Collection
    {
        $startDate = $filters['date_from'] ?? now()->startOfMonth()->toDateString();
        $endDate = $filters['date_to'] ?? now()->endOfMonth()->toDateString();

        return match($type) {
            'marketplace' => $this->repository->getMarketplaceStats($startDate, $endDate),
            'reason' => $this->repository->getReasonStats($startDate, $endDate),
            'warehouse' => $this->repository->getWarehouseLossStats($startDate, $endDate),
            'detailed' => $this->repository->all($filters),
            default => collect([]),
        };
    }
}
