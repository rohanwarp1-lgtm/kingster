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
use App\Models\MailTemplate;
use App\Modules\Warranty\Notifications\WarrantySubmittedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
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

        $months = FbaAuto::query()
            ->selectRaw('YEAR(shipment_date) as yr, MONTH(shipment_date) as mn')
            ->whereNotNull('shipment_date')
            ->groupBy('yr', 'mn')
            ->orderByDesc('yr')
            ->orderByDesc('mn')
            ->get()
            ->mapWithKeys(fn ($row) => [
                $row->yr . '-' . str_pad($row->mn, 2, '0', STR_PAD_LEFT)
                    => Carbon::createFromDate($row->yr, $row->mn, 1)->format('F Y'),
            ]);

        return view('admin.modules.fba-auto.index', compact('stats', 'states', 'warehouses', 'productNames', 'months'));
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
        $month  = $request->get('month');
        $state  = $request->get('state');
        $status = $request->get('status_filter');
        $date   = $request->get('date_filter'); // 'today', 'yesterday', ''

        $shipments = FbaAuto::query()
            ->with(['generator', 'updater'])
            ->when($month, function ($q) use ($month) {
                [$yr, $mn] = explode('-', $month);
                $q->whereYear('shipment_date', $yr)->whereMonth('shipment_date', $mn);
            })
            ->when($state, fn ($q) => $q->where('state', $state))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($date === 'today', fn ($q) => $q->whereDate('created_at', today()))
            ->when($date === 'yesterday', fn ($q) => $q->whereDate('created_at', today()->subDay()))
            ->orderByDesc('shipment_date')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('shipment_id')
            ->map(function ($items) {
                $first        = $items->first();
                $latestUpdate = $items->sortByDesc('updated_at')->first();

                return (object) [
                    'id'                => $first->id,
                    'shipment_id'       => $first->shipment_id,
                    'shipment_date'     => $first->shipment_date,
                    'shipment_date_sort'=> $first->created_at?->format('Y-m-d H:i:s') ?? ($first->shipment_date?->format('Y-m-d') ?? ''),
                    'product_names'     => $items->pluck('product_name')->values()->all(),
                    'asin_values'       => $items->pluck('asin')->values()->all(),
                    'sku_values'        => $items->pluck('sku')->values()->all(),
                    'sku_label_values'  => $items->pluck('sku_label')->values()->all(),
                    'qty_values'        => $items->pluck('qty')->values()->all(),
                    'qty_price_values'  => $items->pluck('qty_price')->values()->all(),
                    'qty_total'         => $items->sum('qty'),
                    'amount_total'      => $items->sum('qty_price'),
                    'state'             => $first->state,
                    'warehouse_name'    => $first->warehouse_name,
                    'generated_by_name' => $first->generator->username ?? $first->generator->name ?? 'System',
                    'generated_at'      => $items->sortBy('created_at')->first()?->created_at,
                    'updated_by_name'   => $latestUpdate?->updater ? ($latestUpdate->updater->username ?? $latestUpdate->updater->name ?? 'System') : null,
                    'updated_at'        => $latestUpdate?->updated_by ? $latestUpdate->updated_at : null,
                    'status'            => $first->status,
                ];
            })
            ->values();

        return datatables()
            ->collection($shipments)
            ->addIndexColumn()
            ->addColumn('shipment_id', fn ($row) =>
                '<strong>'.e($row->shipment_id).'</strong>'.
                '<div class="fba-sub-text"><i class="fe fe-user me-1"></i>'.e($row->generated_by_name).
                ' &nbsp;<i class="fe fe-clock me-1"></i>'.optional($row->generated_at)->format('d-M-Y H:i').'</div>'
            )
            ->addColumn('shipment_date', fn ($row) => optional($row->shipment_date)->format('d-M-Y'))
            ->addColumn('product_name', fn ($row) => $this->formatFbaProductLines($row))
            ->addColumn('qty', fn ($row) => $this->formatFbaLines(array_map(fn ($qty) => number_format((int) $qty), $row->qty_values)))
            ->addColumn('warehouse_name', fn ($row) => '<span class="badge bg-primary">'.e($row->warehouse_name).'</span>')
            ->addColumn('qty_price', fn ($row) => $this->formatFbaLines(array_map(fn ($price) => '₹' . number_format((float) $price, 2), $row->qty_price_values)))
            ->addColumn('status', fn ($row) => $this->fbaStatusBadge($row->status))
            ->addColumn('action', fn ($row) =>
                '<a href="'.route('admin.fba-auto.show', $row->id).'" class="btn btn-sm btn-info me-1 text-white" title="View history"><i class="fe fe-eye"></i></a>'.
                '<button class="btn btn-sm btn-primary edit-btn me-1" data-id="'.$row->id.'" title="Edit"><i class="fe fe-edit"></i></button>'.
                '<button class="btn btn-sm btn-danger delete-btn" data-id="'.$row->id.'"><i class="fe fe-trash-2"></i></button>'
            )
            ->rawColumns(['shipment_id', 'product_name', 'qty', 'warehouse_name', 'qty_price', 'status', 'action'])
            ->make(true);
    }

    public function fbaAutoFilterSummary(Request $request)
    {
        $month  = $request->get('month');
        $state  = $request->get('state');
        $status = $request->get('status_filter');
        $date   = $request->get('date_filter');

        $query = FbaAuto::query()
            ->when($month, function ($q) use ($month) {
                [$yr, $mn] = explode('-', $month);
                $q->whereYear('shipment_date', $yr)->whereMonth('shipment_date', $mn);
            })
            ->when($state, fn ($q) => $q->where('state', $state))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($date === 'today', fn ($q) => $q->whereDate('created_at', today()))
            ->when($date === 'yesterday', fn ($q) => $q->whereDate('created_at', today()->subDay()));

        $totalQty    = (int) $query->sum('qty');
        $totalAmount = (float) $query->sum('qty_price');

        $byState = (clone $query)
            ->selectRaw('state, SUM(qty) as qty, SUM(qty_price) as amount, COUNT(DISTINCT shipment_id) as shipments')
            ->groupBy('state')
            ->orderByDesc('amount')
            ->get();

        return response()->json([
            'success' => true,
            'total_qty'    => $totalQty,
            'total_amount' => $totalAmount,
            'by_state'     => $byState,
        ]);
    }

    public function fbaAutoExport(Request $request)
    {
        $month  = $request->get('month');
        $state  = $request->get('state');
        $status = $request->get('status_filter');
        $date   = $request->get('date_filter');

        $rows = FbaAuto::query()
            ->when($month, function ($q) use ($month) {
                [$yr, $mn] = explode('-', $month);
                $q->whereYear('shipment_date', $yr)->whereMonth('shipment_date', $mn);
            })
            ->when($state, fn ($q) => $q->where('state', $state))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($date === 'today', fn ($q) => $q->whereDate('created_at', today()))
            ->when($date === 'yesterday', fn ($q) => $q->whereDate('created_at', today()->subDay()))
            ->orderByDesc('shipment_date')->orderByDesc('created_at')
            ->get();

        $filename = 'fba_shipments_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($rows) {
            $fp = fopen('php://output', 'w');
            fputcsv($fp, ['Shipment ID', 'Shipment Date', 'Product', 'ASIN', 'SKU', 'SKU Label', 'Qty', 'Amount (₹)', 'State', 'Warehouse', 'Status', 'Created At']);
            foreach ($rows as $row) {
                fputcsv($fp, [
                    $row->shipment_id,
                    $row->shipment_date?->format('d-M-Y'),
                    $row->product_name,
                    $row->asin,
                    $row->sku,
                    $row->sku_label,
                    $row->qty,
                    $row->qty_price,
                    $row->state,
                    $row->warehouse_name,
                    $row->status,
                    $row->created_at?->format('d-M-Y H:i'),
                ]);
            }
            fclose($fp);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function fbaAutoReportData(Request $request)
    {
        $month = $request->get('month');
        $state = $request->get('state');

        $base = FbaAuto::query()
            ->when($month, function ($q) use ($month) {
                [$yr, $mn] = explode('-', $month);
                $q->whereYear('shipment_date', $yr)->whereMonth('shipment_date', $mn);
            })
            ->when($state, fn ($q) => $q->where('state', $state));

        $byState = (clone $base)
            ->selectRaw('state, SUM(qty) as total_qty, SUM(qty_price) as total_amount, COUNT(DISTINCT shipment_id) as shipments')
            ->groupBy('state')
            ->orderByDesc('total_amount')
            ->get();

        $byProduct = (clone $base)
            ->selectRaw('product_name, SUM(qty) as total_qty, SUM(qty_price) as total_amount')
            ->groupBy('product_name')
            ->orderByDesc('total_amount')
            ->get();

        $overall = [
            'total_qty'       => (int) (clone $base)->sum('qty'),
            'total_amount'    => (float) (clone $base)->sum('qty_price'),
            'total_shipments' => (clone $base)->distinct('shipment_id')->count('shipment_id'),
        ];

        return response()->json([
            'success'    => true,
            'overall'    => $overall,
            'by_state'   => $byState,
            'by_product' => $byProduct,
        ]);
    }

    private function formatFbaProductLines(object $row): string
    {
        $lines = '';
        foreach ($row->product_names as $i => $name) {
            $sub = '';
            if (!empty($row->asin_values[$i])) $sub .= '<span class="fba-tag">ASIN:'.e($row->asin_values[$i]).'</span>';
            if (!empty($row->sku_values[$i]))  $sub .= '<span class="fba-tag">SKU:'.e($row->sku_values[$i]).'</span>';
            if (!empty($row->sku_label_values[$i])) $sub .= '<span class="fba-tag">LBL:'.e($row->sku_label_values[$i]).'</span>';
            $lines .= '<div class="fba-merged-line">'.e($name).($sub ? '<div class="fba-tags">'.$sub.'</div>' : '').'</div>';
        }
        return $lines;
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
        $stats    = $this->warrantyService->getDashboardStats();
        $statuses = ['pending', 'under_review', 'approved', 'rejected', 'cancelled'];
        $months   = WarrantyRegistration::query()
            ->selectRaw('YEAR(created_at) as yr, MONTH(created_at) as mn')
            ->groupBy('yr', 'mn')
            ->orderByDesc('yr')->orderByDesc('mn')
            ->get()
            ->mapWithKeys(fn ($row) => [
                $row->yr . '-' . str_pad($row->mn, 2, '0', STR_PAD_LEFT)
                    => Carbon::createFromDate($row->yr, $row->mn, 1)->format('F Y'),
            ]);
        return view('admin.modules.warranty.index', compact('stats', 'statuses', 'months'));
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
        $month  = $request->get('month');
        $status = $request->get('status');

        $query = WarrantyRegistration::query()
            ->select('warranty_registrations.*')
            ->when($month, function ($q) use ($month) {
                [$yr, $mn] = explode('-', $month);
                $q->whereYear('created_at', $yr)->whereMonth('created_at', $mn);
            })
            ->when($status, fn ($q) => $q->where('status', $status));

        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('purchase_date', fn ($row) => optional($row->purchase_date)->format('Y-m-d'))
            ->addColumn('expiry_date', fn ($row) => optional($row->expiry_date)->format('Y-m-d'))
            ->addColumn('status', fn ($row) => $row->status_badge)
            ->addColumn('action', fn ($row) =>
                '<a href="/admin/warranty/show/'.$row->id.'" class="btn btn-sm btn-info me-1 text-white"><i class="fe fe-eye"></i></a>'.
                '<div class="dropdown d-inline-block me-1">'.
                  '<button class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" title="Change Status"><i class="fe fe-sliders"></i></button>'.
                  '<ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width:140px;">'.
                    '<li><h6 class="dropdown-header" style="font-size:11px;">Change Status</h6></li>'.
                    '<li><a class="dropdown-item status-change-btn" href="#" data-id="'.$row->id.'" data-status="pending"><span class="badge bg-warning me-2">●</span>Pending</a></li>'.
                    '<li><a class="dropdown-item status-change-btn" href="#" data-id="'.$row->id.'" data-status="approved"><span class="badge bg-success me-2">●</span>Active</a></li>'.
                    '<li><a class="dropdown-item status-change-btn" href="#" data-id="'.$row->id.'" data-status="expired"><span class="badge bg-secondary me-2">●</span>Expired</a></li>'.
                    '<li><a class="dropdown-item status-change-btn" href="#" data-id="'.$row->id.'" data-status="rejected"><span class="badge bg-danger me-2">●</span>Rejected</a></li>'.
                  '</ul>'.
                '</div>'.
                '<button class="btn btn-sm btn-danger delete-btn" data-id="'.$row->id.'" title="Delete"><i class="fe fe-trash-2"></i></button>'
            )
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function warrantyStats()
    {
        return response()->json(['success' => true, 'data' => $this->warrantyService->getDashboardStats()]);
    }

    public function warrantyChangeStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,expired',
            'notes'  => 'required_if:status,rejected|nullable|string|max:1000',
        ], [
            'notes.required_if' => 'A reason is required when rejecting a warranty.',
        ]);

        try {
            $this->warrantyService->changeStatus($id, $request->status, $request->notes);
            $label = ['pending' => 'Pending', 'approved' => 'Active', 'rejected' => 'Rejected', 'expired' => 'Expired'][$request->status];
            return response()->json(['success' => true, 'message' => 'Status changed to ' . $label . ' and notification sent.']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    // MAIL TEMPLATE – Warranty Registration
    public function rmaIndex()
    {
        $stats    = $this->rmaService->getDashboardStats();
        $statuses = ['open', 'under_review', 'approved', 'pickup_pending', 'closed'];
        $months   = RmaTicket::query()
            ->selectRaw('YEAR(created_at) as yr, MONTH(created_at) as mn')
            ->groupBy('yr', 'mn')
            ->orderByDesc('yr')->orderByDesc('mn')
            ->get()
            ->mapWithKeys(fn ($row) => [
                $row->yr . '-' . str_pad($row->mn, 2, '0', STR_PAD_LEFT)
                    => Carbon::createFromDate($row->yr, $row->mn, 1)->format('F Y'),
            ]);
        return view('admin.modules.rma.index', compact('stats', 'statuses', 'months'));
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
        $month  = $request->get('month');
        $status = $request->get('status');

        $query = RmaTicket::query()
            ->select('rma_tickets.*')
            ->when($month, function ($q) use ($month) {
                [$yr, $mn] = explode('-', $month);
                $q->whereYear('created_at', $yr)->whereMonth('created_at', $mn);
            })
            ->when($status, fn ($q) => $q->where('status', $status));

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
        $marketplaces = $this->returnReportRepository->getMarketplaces();
        $months       = ReturnReport::query()
            ->selectRaw('YEAR(created_at) as yr, MONTH(created_at) as mn')
            ->groupBy('yr', 'mn')
            ->orderByDesc('yr')->orderByDesc('mn')
            ->get()
            ->mapWithKeys(fn ($row) => [
                $row->yr . '-' . str_pad($row->mn, 2, '0', STR_PAD_LEFT)
                    => Carbon::createFromDate($row->yr, $row->mn, 1)->format('F Y'),
            ]);
        return view('admin.modules.return-report.index', compact('months', 'marketplaces'));
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
        $month       = $request->get('month');
        $marketplace = $request->get('marketplace');

        $query = ReturnReport::query()
            ->select('return_reports.*')
            ->when($month, function ($q) use ($month) {
                [$yr, $mn] = explode('-', $month);
                $q->whereYear('created_at', $yr)->whereMonth('created_at', $mn);
            })
            ->when($marketplace, fn ($q) => $q->where('marketplace', $marketplace));

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

    // MAIL TEMPLATES – All warranty types
    public function warrantyMailTemplate(Request $request)
    {
        $type     = $request->get('type', 'warranty_registration');
        $template = MailTemplate::where('type', $type)->first();
        return response()->json(['success' => true, 'data' => $template]);
    }

    public function warrantyMailTemplateSave(Request $request)
    {
        $request->validate([
            'type'    => 'required|in:warranty_registration,warranty_active,warranty_rejected,warranty_expired',
            'subject' => 'required|string|max:255',
            'body'    => 'required|string',
        ]);

        $names = [
            'warranty_registration' => 'Warranty Registration Confirmation',
            'warranty_active'       => 'Warranty Activated',
            'warranty_rejected'     => 'Warranty Rejected',
            'warranty_expired'      => 'Warranty Expired',
        ];

        MailTemplate::updateOrCreate(
            ['type' => $request->type],
            ['name' => $names[$request->type], 'subject' => $request->subject, 'body' => $request->body, 'is_active' => true]
        );

        return response()->json(['success' => true, 'message' => 'Template saved successfully']);
    }

    public function warrantyMailTemplateSendTest(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'type'  => 'required|in:warranty_registration,warranty_active,warranty_rejected,warranty_expired',
        ]);

        try {
            $template = MailTemplate::getTemplate($request->type);

            $dummyWarranty = (object) [
                'ticket_no'         => 'WARR-TEST001',
                'customer_name'     => 'Test Customer',
                'product_name'      => 'Kingster Sample Product',
                'model'             => 'KG-2024-X',
                'serial_number'     => 'SN123456789',
                'purchase_platform' => 'Amazon',
                'purchase_date'     => now(),
                'expiry_date'       => now()->addYear(),
                'warranty_type'     => 'standard',
                'email'             => $request->email,
            ];

            $vars = [
                'customer_name' => 'Test Customer',
                'ticket_no'     => 'WARR-TEST001',
                'product_name'  => 'Kingster Sample Product',
                'model'         => 'KG-2024-X',
                'serial_number' => 'SN123456789',
                'purchase_date' => now()->format('d M Y'),
                'expiry_date'   => now()->addYear()->format('d M Y'),
                'warranty_type' => 'Standard',
                'reason'        => 'Documentation incomplete (test)',
            ];

            if ($template) {
                ['subject' => $subject, 'body' => $body] = $template->render($vars);
            } else {
                $subject = 'Kingster – Mail Test';
                $body    = '<p>This is a test email from the Kingster admin panel.</p>';
            }

            $statusMap = [
                'warranty_registration' => ['view' => 'emails.warranty.registration', 'status' => null,       'title' => null],
                'warranty_active'       => ['view' => 'emails.warranty.status-update', 'status' => 'approved', 'title' => 'Warranty Activated!'],
                'warranty_rejected'     => ['view' => 'emails.warranty.status-update', 'status' => 'rejected', 'title' => 'Warranty Not Approved'],
                'warranty_expired'      => ['view' => 'emails.warranty.status-update', 'status' => 'expired',  'title' => 'Warranty Expired'],
            ];

            $map      = $statusMap[$request->type];
            $viewData = ['subject' => $subject, 'body' => $body, 'warranty' => $dummyWarranty];

            if ($map['status']) {
                $viewData['status']      = $map['status'];
                $viewData['headerTitle'] = $map['title'];
            }

            Mail::send($map['view'], $viewData, fn ($msg) => $msg->to($request->email)->subject($subject));

            return response()->json(['success' => true, 'message' => 'Test mail sent to ' . $request->email]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed: ' . $e->getMessage()], 500);
        }
    }
}
