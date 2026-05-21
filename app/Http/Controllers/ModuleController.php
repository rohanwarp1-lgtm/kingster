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
use App\Models\ProductName;
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

    // FBA Shipment MODULE
    public function fbaAutoIndex()
    {
        $stats = $this->fbaAutoService->getDashboardStats();
        $states = $this->fbaAutoRepository->getStates();
        $warehouses = $this->fbaAutoRepository->getWarehouses();
        $productNames = ProductName::query()
            ->where('is_deleted', 0)
            ->orderBy('name')
            ->pluck('name')
            ->toArray();

        return view('admin.modules.fba-auto.index', compact('stats', 'states', 'warehouses', 'productNames'));
    }

    public function fbaAutoCreate()
    {
        return redirect()->route('admin.fba-auto.index');
    }

    public function fbaAutoStore(StoreFbaAutoRequest $request)
    {
        try {
            $shipment = $this->fbaAutoService->createShipment($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Shipment created successfully',
                'data' => $shipment,
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function fbaAutoEdit($id)
    {
        $shipment = $this->fbaAutoRepository->find($id);
        if (!$shipment) {
            return response('Shipment not found', 404);
        }

        $items      = $this->fbaAutoRepository->getByShipmentId($shipment->shipment_id);
        $warehouses = $this->fbaAutoRepository->getWarehouses();
        $states     = $this->fbaAutoRepository->getStates();

        return view('admin.modules.fba-auto.edit', compact('shipment', 'items', 'warehouses', 'states'));
    }

    public function fbaAutoShow($id)
    {
        $shipment = $this->fbaAutoRepository->find($id);

        if ($shipment) {
            $items = $this->fbaAutoRepository->getByShipmentId($shipment->shipment_id);
            $shipment->load(['activities' => fn ($query) => $query->with('causer')->latest()]);
        } else {
            $items = collect();
        }

        return view('admin.modules.fba-auto.show', compact('shipment', 'items'));
    }

    public function fbaAutoUpdate(UpdateFbaAutoRequest $request, $id)
    {
        try {
            $shipment = $this->fbaAutoRepository->find($id);
            if (!$shipment) {
                return response()->json(['success' => false, 'message' => 'Shipment not found'], 404);
            }
            $this->fbaAutoService->updateShipmentFull($shipment->shipment_id, $request->validated());
            return response()->json(['success' => true, 'message' => 'Shipment updated successfully']);
        } catch (Exception $e) {
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
        $shipments = FbaAuto::query()
            ->with(['generator', 'updater'])
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('shipment_id')
            ->map(function ($items) {
                $first = $items->first();
                $latestUpdate = $items->sortByDesc('updated_at')->first();

                return (object) [
                    'id' => $first->id,
                    'shipment_id' => $first->shipment_id,
                    'shipment_date' => $first->shipment_date,
                    'product_names' => $items->pluck('product_name')->values()->all(),
                    'qty_values' => $items->pluck('qty')->values()->all(),
                    'qty_price_values' => $items->pluck('qty_price')->values()->all(),
                    'state' => $first->state,
                    'warehouse_name' => $first->warehouse_name,
                    'generated_by_name' => $first->generator->username ?? $first->generator->name ?? 'System',
                    'generated_at' => $items->sortBy('created_at')->first()?->created_at,
                    'updated_by_name' => $latestUpdate?->updater ? ($latestUpdate->updater->username ?? $latestUpdate->updater->name ?? 'System') : null,
                    'updated_at' => $latestUpdate?->updated_by ? $latestUpdate->updated_at : null,
                    'status' => $first->status,
                ];
            })
            ->values();

        return datatables()
            ->collection($shipments)
            ->addIndexColumn()
            ->addColumn('shipment_id', fn ($row) => '<strong>'.e($row->shipment_id).'</strong>')
            ->addColumn('shipment_date', fn ($row) => optional($row->shipment_date)->format('d-M-Y'))
            ->addColumn('product_name', fn ($row) => $this->formatFbaLines($row->product_names))
            ->addColumn('qty', fn ($row) => $this->formatFbaLines(array_map(fn ($qty) => number_format((int) $qty), $row->qty_values)))
            ->addColumn('warehouse_name', fn ($row) => '<span class="badge bg-primary">'.e($row->warehouse_name).'</span>')
            ->addColumn('qty_price', fn ($row) => $this->formatFbaLines(array_map(fn ($price) => '₹' . number_format((float) $price, 2), $row->qty_price_values)))
            ->addColumn('generated_by', fn ($row) => e($row->generated_by_name))
            ->addColumn('generated_at', fn ($row) => optional($row->generated_at)->format('d-M-Y H:i'))
            ->addColumn('updated_by', fn ($row) => $row->updated_by_name ? e($row->updated_by_name) : '-')
            ->addColumn('updated_at', fn ($row) => $row->updated_at ? optional($row->updated_at)->format('d-M-Y H:i') : '-')
            ->addColumn('status', fn ($row) => $this->fbaStatusBadge($row->status))
            ->addColumn('action', fn ($row) =>
                '<a href="'.route('admin.fba-auto.show', $row->id).'" class="btn btn-sm btn-info me-1 text-white" title="View history"><i class="fe fe-eye"></i></a>'.
                '<button class="btn btn-sm btn-primary edit-btn me-1" data-id="'.$row->id.'" title="Edit"><i class="fe fe-edit"></i></button>'.
                '<button class="btn btn-sm btn-danger delete-btn" data-id="'.$row->id.'"><i class="fe fe-trash-2"></i></button>'
            )
            ->rawColumns(['shipment_id', 'product_name', 'qty', 'warehouse_name', 'qty_price', 'status', 'action'])
            ->make(true);
    }

    private function formatFbaLines(array $values): string
    {
        return collect($values)
            ->map(fn ($value) => '<div class="fba-merged-line">'.e((string) $value).'</div>')
            ->implode('');
    }

    private function fbaStatusBadge(string $status): string
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'processing' => '<span class="badge bg-info">Processing</span>',
            'shipped' => '<span class="badge bg-primary">Shipped</span>',
            'delivered' => '<span class="badge bg-success">Delivered</span>',
            'closed' => '<span class="badge bg-secondary">Closed</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
            'returned' => '<span class="badge bg-dark">Returned</span>',
        ];

        return $badges[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
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
        return redirect()->route('admin.warranty.index');
    }

    public function warrantyStore(StoreWarrantyRequest $request)
    {
        try {
            $warranty = $this->warrantyService->register($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Warranty registered successfully',
                'data' => ['ticket_no' => $warranty->ticket_no, 'id' => $warranty->id],
            ], 201);
        } catch (Exception $e) {
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
            $this->warrantyService->reject($id, $request->input('reason', $request->input('notes', 'Rejected by admin')));
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
                '<a href="/admin/warranty/show/'.$row->id.'" class="btn btn-sm btn-info me-1 text-white"><i class="fe fe-eye"></i></a>'.
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
        return redirect()->route('admin.rma.index');
    }

    public function rmaStore(StoreRmaTicketRequest $request)
    {
        try {
            $ticket = $this->rmaService->createTicket($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Ticket created successfully',
                'data' => ['ticket_id' => $ticket->ticket_id, 'id' => $ticket->id],
            ], 201);
        } catch (Exception $e) {
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
                '<a href="/admin/rma/show/'.$row->id.'" class="btn btn-sm btn-info me-1 text-white"><i class="fe fe-eye"></i></a>'.
                '<button class="btn btn-sm btn-warning status-btn me-1 text-white" data-id="'.$row->id.'"><i class="fe fe-edit"></i></button>'.
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
        return redirect()->route('admin.return-report.index');
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
                '<a href="/admin/return-report/show/'.$row->id.'" class="btn btn-sm btn-info me-1 text-white"><i class="fe fe-eye"></i></a>'.
                '<button class="btn btn-sm btn-danger delete-btn" data-id="'.$row->id.'"><i class="fe fe-trash-2"></i></button>'
            )
            ->rawColumns(['action'])
            ->make(true);
    }
}
