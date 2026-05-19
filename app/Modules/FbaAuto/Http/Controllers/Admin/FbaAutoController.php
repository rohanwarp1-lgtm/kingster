<?php

namespace App\Modules\FbaAuto\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\FbaAuto\Models\FbaAuto;
use App\Modules\FbaAuto\Services\FbaAutoService;
use App\Modules\FbaAuto\DataTables\FbaAutoDataTable;
use App\Modules\FbaAuto\Http\Requests\StoreFbaAutoRequest;
use App\Modules\FbaAuto\Http\Requests\UpdateFbaAutoRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Exception;

class FbaAutoController extends Controller
{
    public function __construct(
        private FbaAutoService $service
    ) {}

    public function index(FbaAutoDataTable $dataTable)
    {
        $warehouses = $this->service->repository->getWarehouses();
        $states = $this->service->repository->getStates();
        $productNames = $this->service->repository->getProductNames();

        return $dataTable->render('admin.modules.fba-auto.index', compact('warehouses', 'states', 'productNames'));
    }

    public function create()
    {
        return redirect()->route('admin.fba-auto.index');
    }

    public function store(StoreFbaAutoRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            $shipment = $this->service->createShipment($request->validated());
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Shipment created successfully',
                'data' => $shipment,
            ], 201);
            
        } catch (Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create shipment: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function edit(int $id)
    {
        $shipment = $this->service->repository->find($id);
        
        if (!$shipment) {
            abort(404, 'Shipment not found');
        }
        
        $warehouses = $this->service->repository->getWarehouses();
        $states = $this->service->repository->getStates();
        
        return view('admin.modules.fba-auto.edit', compact('shipment', 'warehouses', 'states'));
    }

    public function update(UpdateFbaAutoRequest $request, int $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            $updated = $this->service->updateShipment($id, $request->validated());
            
            DB::commit();
            
            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipment not found',
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Shipment updated successfully',
            ]);
            
        } catch (Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update shipment: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->service->deleteShipment($id);
            
            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipment not found',
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Shipment deleted successfully',
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete shipment: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function restore(int $id): JsonResponse
    {
        try {
            $restored = $this->service->restoreShipment($id);
            
            if (!$restored) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipment not found or not deleted',
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Shipment restored successfully',
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore shipment: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function changeStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,closed,cancelled,returned',
            'notes' => 'nullable|string|max:500',
        ]);
        
        try {
            $this->service->updateStatus($id, $request->status, $request->notes);
            
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

    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
            'action' => 'required|in:delete,restore,status',
            'status' => 'required_if:action,status|in:pending,processing,shipped,delivered,closed,cancelled,returned',
        ]);
        
        try {
            $ids = $request->ids;
            $action = $request->action;
            
            switch ($action) {
                case 'delete':
                    $results = $this->service->bulkDelete($ids);
                    break;
                    
                case 'restore':
                    $results = $this->service->bulkRestore($ids);
                    break;
                    
                case 'status':
                    $results = $this->service->bulkStatusUpdate($ids, $request->status);
                    break;
                    
                default:
                    throw new Exception('Invalid action');
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Bulk action completed',
                'results' => $results,
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk action failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function ajax(Request $request): JsonResponse
    {
        return $this->service->getFilteredData($request->all());
    }

    public function getStats(): JsonResponse
    {
        $stats = $this->service->getDashboardStats();
        
        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
