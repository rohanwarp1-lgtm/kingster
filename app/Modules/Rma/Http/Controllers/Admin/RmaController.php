<?php

namespace App\Modules\Rma\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Rma\Services\RmaWorkflowService;
use App\Modules\Rma\DataTables\RmaTicketDataTable;
use App\Modules\Rma\Http\Requests\StoreRmaTicketRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Exception;

class RmaController extends Controller
{
    public function __construct(
        private RmaWorkflowService $workflowService
    ) {}

    public function index(RmaTicketDataTable $dataTable)
    {
        $stats = $this->workflowService->getDashboardStats();

        return $dataTable->render('admin.modules.rma.index', compact('stats'));
    }

    public function create()
    {
        return redirect()->route('admin.rma.index');
    }

    public function store(StoreRmaTicketRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            $ticket = $this->workflowService->createTicket($request->validated());
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'RMA ticket created successfully',
                'data' => [
                    'ticket_id' => $ticket->ticket_id,
                    'id' => $ticket->id,
                ],
            ], 201);
            
        } catch (Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(int $id)
    {
        $ticket = $this->workflowService->repository->find($id);
        
        if (!$ticket) {
            abort(404, 'Ticket not found');
        }
        
        return view('admin.modules.rma.show', compact('ticket'));
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:open,under_review,approved,rejected,pickup_pending,pickup_completed,replacement_shipped,closed',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        try {
            $ticket = $this->workflowService->repository->find($id);
            
            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found',
                ], 404);
            }

            $this->workflowService->transition($ticket, $request->status, $request->notes);
            
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function assign(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'assigned_to' => 'required|integer|exists:users,id',
        ]);
        
        try {
            $ticket = $this->workflowService->repository->find($id);
            
            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found',
                ], 404);
            }

            $this->workflowService->assignTicket($ticket, $request->assigned_to);
            
            return response()->json([
                'success' => true,
                'message' => 'Ticket assigned successfully',
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function addComment(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'content' => 'required|string|max:5000',
            'is_internal' => 'sometimes|boolean',
        ]);
        
        try {
            $ticket = $this->workflowService->repository->find($id);
            
            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found',
                ], 404);
            }

            $comment = $this->workflowService->addComment(
                $ticket,
                $request->content,
                $request->boolean('is_internal')
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'data' => $comment,
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->workflowService->repository->delete($id);
            
            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found',
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Ticket deleted successfully',
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete ticket: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getStats(): JsonResponse
    {
        $stats = $this->workflowService->getDashboardStats();
        
        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    public function ajax(Request $request)
    {
        $filters = $request->all();
        $data = $this->workflowService->repository->all($filters);
        
        return response()->json($data);
    }
}
