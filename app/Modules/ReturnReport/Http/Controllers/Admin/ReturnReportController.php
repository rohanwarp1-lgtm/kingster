<?php

namespace App\Modules\ReturnReport\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\ReturnReport\Services\AnalyticsService;
use App\Modules\ReturnReport\Services\ExportService;
use App\Modules\ReturnReport\Repositories\ReturnReportRepository;
use App\Modules\ReturnReport\DataTables\ReturnReportDataTable;
use App\Modules\ReturnReport\Http\Requests\StoreReturnReportRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class ReturnReportController extends Controller
{
    public function __construct(
        private AnalyticsService $analyticsService,
        private ExportService $exportService,
        private ReturnReportRepository $repository
    ) {}

    public function index(ReturnReportDataTable $dataTable)
    {
        return $dataTable->render('admin.modules.return-report.index');
    }

    public function create()
    {
        $warehouses = $this->repository->getWarehouses();
        $reasons = $this->repository->getReturnReasons();
        
        return redirect()->route('admin.return-report.index');
    }

    public function store(StoreReturnReportRequest $request): JsonResponse
    {
        try {
            $report = $this->repository->create($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Return report created successfully',
                'data' => $report,
            ], 201);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create report: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(int $id)
    {
        $report = $this->repository->find($id);
        
        if (!$report) {
            abort(404, 'Report not found');
        }
        
        return view('admin.modules.return-report.show', compact('report'));
    }

    public function dashboard(Request $request): JsonResponse
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());
        $type = $request->get('type', 'daily');

        $stats = $this->analyticsService->getDashboardStats($startDate, $endDate);
        $kpis = $this->analyticsService->getKpiMetrics($startDate, $endDate);
        $report = $this->analyticsService->getReport($type, [
            'date_from' => $startDate,
            'date_to' => $endDate,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'kpis' => $kpis,
                'report' => $report,
            ],
        ]);
    }

    public function export(Request $request)
    {
        $filters = $request->all();
        $format = $request->get('format', 'excel');

        try {
            if ($format === 'csv') {
                return $this->exportService->exportToCsv($filters);
            }

            return $this->exportService->exportToExcel($filters);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Export failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->repository->delete($id);
            
            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Report not found',
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Report deleted successfully',
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete report: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function ajax(Request $request)
    {
        $filters = $request->all();
        $data = $this->repository->all($filters);
        
        return response()->json($data);
    }

    public function getFilters(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'warehouses' => $this->repository->getWarehouses(),
                'reasons' => $this->repository->getReturnReasons(),
                'marketplaces' => $this->repository->getMarketplaces(),
            ],
        ]);
    }
}
