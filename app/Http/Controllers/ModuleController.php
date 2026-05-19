<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\FbaAuto\Services\FbaAutoService;
use App\Modules\FbaAuto\Repositories\FbaAutoRepository;
use App\Modules\Warranty\Services\WarrantyService;
use App\Modules\Warranty\Repositories\WarrantyRepository;
use App\Modules\Rma\Services\RmaWorkflowService;
use App\Modules\Rma\Repositories\RmaRepository;
use App\Modules\ReturnReport\Services\AnalyticsService;
use App\Modules\ReturnReport\Services\ExportService;
use App\Modules\ReturnReport\Repositories\ReturnReportRepository;
use App\Modules\FbaAuto\Http\Requests\StoreFbaAutoRequest;
use App\Modules\FbaAuto\Http\Requests\UpdateFbaAutoRequest;
use App\Modules\Warranty\Http\Requests\StoreWarrantyRequest;
use App\Modules\Rma\Http\Requests\StoreRmaTicketRequest;
use App\Modules\ReturnReport\Http\Requests\StoreReturnReportRequest;
use App\Modules\FbaAuto\Models\FbaAuto;
use App\Modules\Warranty\Models\WarrantyRegistration;
use App\Modules\Rma\Models\RmaTicket;
use App\Modules\ReturnReport\Models\ReturnReport;
use Illuminate\Support\Facades\DB;
use Exception;

class ModuleController extends Controller
{
    public function __construct(
        private FbaAutoService $fbaAutoService,
        private FbaAutoRepository $fbaAutoRepository,
        private WarrantyService $warrantyService,
        private WarrantyRepository $warrantyRepository,
        private RmaWorkflowService $rmaService,
        private RmaRepository $rmaRepository,
        private AnalyticsService $analyticsService,
        private ExportService $exportService,
        private ReturnReportRepository $returnReportRepository
    ) {}

    // FBA AUTO MODULE
    public function fbaAutoIndex()
    {
        $stats = $this->fbaAutoService->getDashboardStats();
        return view('admin.modules.fba-auto.index', ['stats' => $stats]);
    }

    public function fbaAutoCreate()
    {
        $warehouses = $this->fbaAutoRepository->getWarehouses();
        $states = $this->fbaAutoRepository->getStates();
        return view('admin.modules.fba-auto.create', compact('warehouses', 'states'));
    }

    public function fbaAutoStore(StoreFbaAutoRequest $request)
    {
        try {
            DB::beginTransaction();
            $shipment = $this->fbaAutoService->createShipment($request->validated());
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Shipment created successfully',
                'data' => $shipment,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function fbaAutoEdit($id)
    {
        $shipment = $this->fbaAutoRepository->find($id);
        $warehouses = $this->fbaAutoRepository->getWarehouses();
        $states = $this->fbaAutoRepository->getStates();
        return view('admin.modules.fba-auto.edit', compact('shipment', 'warehouses', 'states'));
    }

    public function fbaAutoUpdate(UpdateFbaAutoRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->fbaAutoService->updateShipment($id, $request->validated());
            DB::commit();
            
            return response()->json(['success' => true, 'message' => 'Updated successfully']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function fbaAutoDelete($id)
    {
        try {
            $this->fbaAutoService->deleteShipment($id);
            return response()->json(['success' => true, 'message' => 'Deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function fbaAutoChangeStatus(Request $request, $id)
    {
        try {
            $this->fbaAutoService->updateStatus($id, $request->status, $request->notes);
            return response()->json(['success' => true, 'message' => 'Status updated']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function fbaAutoAjax(Request $request)
    {
        $query = FbaAuto::query()->select('fba_autos.*');

        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('shipment_date', fn ($row) => optional($row->shipment_date)->format('Y-m-d'))
            ->addColumn('status', fn ($row) => $row->status_badge)
            ->addColumn('action', fn ($row) =>
                '<button class="btn btn-sm btn-info edit-btn me-1" data-id="'.$row->id.'"><i class="fe fe-edit"></i></button>'.
                '<button class="btn btn-sm btn-danger delete-btn" data-id="'.$row->id.'"><i class="fe fe-trash-2"></i></button>'
            )
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function fbaAutoStats()
    {
        return response()->json(['success' => true, 'data' => $this->fbaAutoService->getDashboardStats()]);
    }

    // WARRANTY MODULE
    public function warrantyIndex()
    {
        $stats = $this->warrantyService->getDashboardStats();
        return view('admin.modules.warranty.index', ['stats' => $stats]);
    }

    public function warrantyCreate()
    {
        return view('admin.modules.warranty.create');
    }

    public function warrantyStore(StoreWarrantyRequest $request)
    {
        try {
            DB::beginTransaction();
            $warranty = $this->warrantyService->register($request->validated());
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Warranty registered successfully',
                'data' => ['ticket_no' => $warranty->ticket_no, 'id' => $warranty->id],
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function warrantyShow($id)
    {
        $warranty = $this->warrantyRepository->find($id);
        return view('admin.modules.warranty.show', compact('warranty'));
    }

    public function warrantyApprove(Request $request, $id)
    {
        try {
            $this->warrantyService->approve($id, $request->notes);
            return response()->json(['success' => true, 'message' => 'Approved successfully']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function warrantyReject(Request $request, $id)
    {
        try {
            $this->warrantyService->reject($id, $request->reason);
            return response()->json(['success' => true, 'message' => 'Rejected successfully']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function warrantyDelete($id)
    {
        try {
            $this->warrantyRepository->delete($id);
            return response()->json(['success' => true, 'message' => 'Deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function warrantyAjax(Request $request)
    {
        $query = WarrantyRegistration::query()->select('warranty_registrations.*');

        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('purchase_date', fn ($row) => optional($row->purchase_date)->format('Y-m-d'))
            ->addColumn('expiry_date', fn ($row) => optional($row->expiry_date)->format('Y-m-d'))
            ->addColumn('status', fn ($row) => $row->status_badge)
            ->addColumn('action', fn ($row) =>
                '<a href="/admin/warranty/show/'.$row->id.'" class="btn btn-sm btn-info me-1"><i class="fe fe-eye"></i></a>'.
                '<button class="btn btn-sm btn-success approve-btn me-1" data-id="'.$row->id.'"><i class="fe fe-check"></i></button>'.
                '<button class="btn btn-sm btn-warning reject-btn me-1" data-id="'.$row->id.'"><i class="fe fe-x"></i></button>'.
                '<button class="btn btn-sm btn-danger delete-btn" data-id="'.$row->id.'"><i class="fe fe-trash-2"></i></button>'
            )
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function warrantyStats()
    {
        return response()->json(['success' => true, 'data' => $this->warrantyService->getDashboardStats()]);
    }

    // RMA MODULE
    public function rmaIndex()
    {
        $stats = $this->rmaService->getDashboardStats();
        return view('admin.modules.rma.index', ['stats' => $stats]);
    }

    public function rmaCreate()
    {
        return view('admin.modules.rma.create');
    }

    public function rmaStore(StoreRmaTicketRequest $request)
    {
        try {
            DB::beginTransaction();
            $ticket = $this->rmaService->createTicket($request->validated());
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Ticket created successfully',
                'data' => ['ticket_id' => $ticket->ticket_id, 'id' => $ticket->id],
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function rmaShow($id)
    {
        $ticket = $this->rmaRepository->find($id);
        return view('admin.modules.rma.show', compact('ticket'));
    }

    public function rmaUpdateStatus(Request $request, $id)
    {
        try {
            $ticket = $this->rmaRepository->find($id);
            $this->rmaService->transition($ticket, $request->status, $request->notes);
            return response()->json(['success' => true, 'message' => 'Status updated']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function rmaAssign(Request $request, $id)
    {
        try {
            $ticket = $this->rmaRepository->find($id);
            $this->rmaService->assignTicket($ticket, $request->assigned_to);
            return response()->json(['success' => true, 'message' => 'Assigned successfully']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function rmaComment(Request $request, $id)
    {
        try {
            $ticket = $this->rmaRepository->find($id);
            $comment = $this->rmaService->addComment($ticket, $request->content, $request->boolean('is_internal'));
            return response()->json(['success' => true, 'message' => 'Comment added', 'data' => $comment]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function rmaDelete($id)
    {
        try {
            $this->rmaRepository->delete($id);
            return response()->json(['success' => true, 'message' => 'Deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function rmaAjax(Request $request)
    {
        $query = RmaTicket::query()->select('rma_tickets.*');

        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('order_date', fn ($row) => optional($row->order_date)->format('Y-m-d'))
            ->addColumn('sla_deadline', fn ($row) => optional($row->sla_deadline)->format('Y-m-d H:i'))
            ->addColumn('status', fn ($row) => $row->status_badge)
            ->addColumn('action', fn ($row) =>
                '<a href="/admin/rma/show/'.$row->id.'" class="btn btn-sm btn-info me-1"><i class="fe fe-eye"></i></a>'.
                '<button class="btn btn-sm btn-warning status-btn me-1" data-id="'.$row->id.'"><i class="fe fe-edit"></i></button>'.
                '<button class="btn btn-sm btn-danger delete-btn" data-id="'.$row->id.'"><i class="fe fe-trash-2"></i></button>'
            )
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function rmaStats()
    {
        return response()->json(['success' => true, 'data' => $this->rmaService->getDashboardStats()]);
    }

    // RETURN REPORT MODULE
    public function returnReportIndex()
    {
        return view('admin.modules.return-report.index');
    }

    public function returnReportCreate()
    {
        $warehouses = $this->returnReportRepository->getWarehouses();
        $reasons = $this->returnReportRepository->getReturnReasons();
        return view('admin.modules.return-report.create', compact('warehouses', 'reasons'));
    }

    public function returnReportStore(StoreReturnReportRequest $request)
    {
        try {
            $report = $this->returnReportRepository->create($request->validated());
            return response()->json(['success' => true, 'message' => 'Report created', 'data' => $report], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function returnReportShow($id)
    {
        $report = $this->returnReportRepository->find($id);
        return view('admin.modules.return-report.show', compact('report'));
    }

    public function returnReportDashboard(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());
        
        $stats = $this->analyticsService->getDashboardStats($startDate, $endDate);
        $kpis = $this->analyticsService->getKpiMetrics($startDate, $endDate);
        
        return response()->json(['success' => true, 'data' => ['stats' => $stats, 'kpis' => $kpis]]);
    }

    public function returnReportExport(Request $request)
    {
        return $this->exportService->exportToExcel($request->all());
    }

    public function returnReportFilters()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'warehouses' => $this->returnReportRepository->getWarehouses(),
                'reasons' => $this->returnReportRepository->getReturnReasons(),
                'marketplaces' => $this->returnReportRepository->getMarketplaces(),
            ]
        ]);
    }

    public function returnReportDelete($id)
    {
        try {
            $this->returnReportRepository->delete($id);
            return response()->json(['success' => true, 'message' => 'Deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function returnReportAjax(Request $request)
    {
        $query = ReturnReport::query()->select('return_reports.*');

        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', fn ($row) =>
                '<a href="/admin/return-report/show/'.$row->id.'" class="btn btn-sm btn-info me-1"><i class="fe fe-eye"></i></a>'.
                '<button class="btn btn-sm btn-danger delete-btn" data-id="'.$row->id.'"><i class="fe fe-trash-2"></i></button>'
            )
            ->rawColumns(['action'])
            ->make(true);
    }
}
