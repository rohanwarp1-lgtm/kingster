<?php

namespace App\Modules\Warranty\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Warranty\Services\WarrantyService;
use App\Modules\Warranty\DataTables\WarrantyDataTable;
use App\Modules\Warranty\Http\Requests\StoreWarrantyRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Exception;

class WarrantyController extends Controller
{
    public function __construct(
        private WarrantyService $service
    ) {}

    public function index(WarrantyDataTable $dataTable)
    {
        return $dataTable->render('admin.modules.warranty.index');
    }

    public function create()
    {
        return redirect()->route('admin.warranty.index');
    }

    public function store(StoreWarrantyRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            $warranty = $this->service->register($request->validated());
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Warranty registration submitted successfully',
                'data' => [
                    'ticket_no' => $warranty->ticket_no,
                    'id' => $warranty->id,
                ],
            ], 201);
            
        } catch (Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit warranty: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(int $id)
    {
        $warranty = $this->service->repository->find($id);
        
        if (!$warranty) {
            abort(404, 'Warranty not found');
        }
        
        return view('admin.modules.warranty.show', compact('warranty'));
    }

    public function startReview(int $id): JsonResponse
    {
        try {
            $this->service->startReview($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Review started successfully',
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function approve(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);
        
        try {
            $this->service->approve($id, $request->notes);
            
            return response()->json([
                'success' => true,
                'message' => 'Warranty approved successfully',
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function reject(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);
        
        try {
            $this->service->reject($id, $request->reason);
            
            return response()->json([
                'success' => true,
                'message' => 'Warranty rejected successfully',
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->service->repository->delete($id);
            
            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Warranty not found',
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Warranty deleted successfully',
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete warranty: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getStats(): JsonResponse
    {
        $stats = $this->service->getDashboardStats();
        
        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    public function ajax(Request $request)
    {
        $filters = $request->all();
        $data = $this->service->repository->all($filters);
        
        return response()->json($data);
    }
}
