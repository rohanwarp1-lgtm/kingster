<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App\Models\Warranty;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;



class WarrantyController extends Controller
{
    public function save(Request $request)
    {
        $type = $request->get('type', 'application');

        if ($type === 'status') {
            $serial = trim((string) $request->get('serial_number', ''));
            $mobile = trim((string) $request->get('mobile_number', ''));

            if ($serial === '' && $mobile === '') {
                return response()->json([
                    'status' => 422,
                    'message' => 'Enter either product serial number or mobile number!',
                ], 422);
            }

            $query = Warranty::query()->where('is_deleted', 0);

            $query->where(function ($q) use ($serial, $mobile) {
                if ($serial !== '') {
                    $q->orWhere('serial_number', $serial);
                }
                if ($mobile !== '') {
                    $q->orWhere('mobile_number', $mobile);
                }
            });

            $records = $query->orderByDesc('id')->limit(10)->get();

            if ($records->isEmpty()) {
                return response()->json([
                    'status' => 4333,
                    'message' => 'No warranty records found.',
                ]);
            }

            $data = $records->map(function ($r) {
                return [
                    'buyer_name' => $r->user_name,
                    'mobile_number' => $r->mobile_number,
                    'email' => $r->email,
                    'purchase_source' => $r->purchase_source,
                    'product_name' => $r->product_name,
                    'serial_number' => $r->serial_number,
                    'purchase_date' => $r->purchase_date ? Carbon::parse($r->purchase_date)->format('d-M-Y') : null,
                    'expiry_date' => $r->expiry_date ? Carbon::parse($r->expiry_date)->format('d-M-Y') : null,
                    'warranty_status' => $r->warranty_status,
                ];
            })->values();

            return response()->json([
                'status' => 200,
                'data' => $data,
            ]);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'buyer_name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'purchase_source' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'product_name' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255'],
            'purchase_date' => ['required', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $serial = trim((string) $request->serial_number);
        $exists = Warranty::query()
            ->where('serial_number', $serial)
            ->where('is_deleted', 0)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 400,
                'message' => 'This serial number already has a warranty application.',
            ]);
        }

        $purchaseDate = Carbon::parse($request->purchase_date);
        $expiryDate = $purchaseDate->copy()->addYear();

        Warranty::create([
            'user_name' => $request->buyer_name,
            'mobile_number' => $request->mobile,
            'email' => $request->email,
            'purchase_source' => $request->purchase_source,
            'address' => $request->address,
            'product_name' => $request->product_name,
            'serial_number' => $serial,
            'purchase_date' => $purchaseDate->toDateString(),
            'expiry_date' => $expiryDate->toDateString(),
            'warranty_status' => 'Pending',
            'is_deleted' => 0,
            'created_by' => 1,
            'modified_by' => 1,
        ]);

        activity('warranty')->causedBy(auth()->user() ?? null)->withProperties(['serial_number' => $serial, 'product' => $request->product_name])->log('created');

        return response()->json([
            'status' => 200,
            'message' => 'Warranty application submitted successfully.',
        ]);
    }

    public function ajax(Request $request){
        try {
            $searchColumns = [
                'warranty_records.id',
                'warranty_records.user_name',
                'warranty_records.mobile_number',
                'warranty_records.email',
                'warranty_records.purchase_source',
                'warranty_records.product_name',
                'warranty_records.serial_number',
                'warranty_records.address',
            ];

            $sortingColumns = [
                0 => 'warranty_records.id',
                1 => 'warranty_records.warranty_status',
                2 => 'warranty_records.user_name',
                3 => 'warranty_records.mobile_number',
                4 => 'warranty_records.product_name',
                5 => 'warranty_records.serial_number',
                6 => 'warranty_records.purchase_date',
                7 => 'warranty_records.expiry_date',
            ];

            $selectColumns = [
                'warranty_records.*',
                'u1.username as created_by_name',
                'u2.username as modified_by_name',
            ];

            // Base query with joins
            $query = Warranty::query()
                ->leftJoin('users as u1', 'u1.id', '=', 'warranty_records.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'warranty_records.modified_by');

            // Total records before filtering
            $recordsTotal = Warranty::count();

            // Filters
            if ($request->filled('warranty_status') && $request->warranty_status != 'All') {
                $query->where('warranty_records.warranty_status', $request->warranty_status);
            }

            if ($request->has('status_filter')) {
                $query->where('warranty_records.is_deleted', $request->status_filter);
            }

            // Search
            if ($request->has('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function ($q) use ($search, $searchColumns) {
                    foreach ($searchColumns as $column) {
                        $q->orWhere($column, 'like', "%$search%");
                    }
                });
            }

            $recordsFiltered = $query->count();

            // Sorting
            if ($request->has('order') && isset($request->order[0])) {
                $colIndex = $request->order[0]['column'];
                $colDir = $request->order[0]['dir'];
                if (isset($sortingColumns[$colIndex])) {
                    $query->orderBy($sortingColumns[$colIndex], $colDir);
                }
            } else {
                $query->orderBy('warranty_records.id', 'desc');
            }

            // Pagination
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $query->skip($start)->take($length != -1 ? $length : $recordsTotal);

            $data = $query->get($selectColumns);

            $viewData = [];
            foreach ($data as $item) {
                $row = [];

                // Action
                $uiAction = '<ul class="list-inline font-size-20 contact-links mb-0">';
                if($item->is_deleted == 0){
                    $uiAction .= '<li class="list-inline-item px-1"><a href="javascript:void(0);" title="Delete" onclick="warrantyDelete(' . $item->id . ')"><i class="fe fe-trash text-danger"></i></a></li>';
                }else{
                    $uiAction .= '<li class="list-inline-item px-1"><a href="javascript:void(0);" title="Restore" onclick="warrantyRestore(' . $item->id . ')"><i class="fe fe-rotate-ccw text-success"></i></a></li>';
                }
                $uiAction .= '<li class="list-inline-item px-1"><a href="javascript:void(0);" title="Change Status" onclick="changeWarrantyStatus(' . $item->id . ', \'' . $item->warranty_status . '\')"><i class="fe fe-sliders text-primary"></i></a></li>';
                $uiAction .= '</ul>';
                
                $row['action'] = $uiAction;

                // Status Badge
                $status = ucfirst($item->warranty_status);
                $colorClass = match ($item->warranty_status) {
                    'Pending'  => 'badge bg-warning',
                    'Active'   => 'badge bg-success',
                    'Expired'  => 'badge bg-danger',
                    'Rejected' => 'badge bg-secondary',
                    default    => 'badge bg-primary'
                };
                $row['warranty_status'] = '<span class="' . $colorClass . '">' . $status . '</span>';

                $row['buyer_name'] = $item->user_name ?? '-';
                $row['mobile'] = $item->mobile_number ?? '-';
                $row['email'] = $item->email ?? '-';
                $row['source'] = $item->purchase_source ?? '-';
                $row['product_name'] = $item->product_name ?? '-';
                $row['serial_number'] = $item->serial_number ?? '-';
                $row['purchase_date'] = $item->purchase_date ? Carbon::parse($item->purchase_date)->format('d-M-Y') : '-';
                $row['expiry_date'] = $item->expiry_date ? Carbon::parse($item->expiry_date)->format('d-M-Y') : '-';
                $row['address'] = $item->address ?? '-';
                $row['created_by'] = $item->created_by_name ?? 'System';
                $row['modified_by'] = $item->modified_by_name ?? '-';

                $viewData[] = $row;
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $viewData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function changeStatus(Request $request){
        try {
            $warranty = Warranty::findOrFail($request->id);
            $warranty->warranty_status = $request->status;
            $warranty->modified_by = Auth::user()->id;
            $warranty->save();

            activity('warranty')->causedBy(auth()->user())->performedOn($warranty)->withProperties(['status' => $request->status])->log('updated');

            return response()->json([
                'status' => 1,
                'message' => 'Warranty status updated successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }

    public function delete(Request $request){
        try {
            $warranty = Warranty::findOrFail($request->id);
            $warranty->is_deleted = 1;
            $warranty->modified_by = Auth::user()->id;
            $warranty->save();
            activity('warranty')->causedBy(auth()->user())->performedOn($warranty)->log('deleted');
            return response()->json(['status' => 1, 'message' => 'Warranty record deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function restore(Request $request){
        try {
            $warranty = Warranty::findOrFail($request->id);
            $warranty->is_deleted = 0;
            $warranty->modified_by = Auth::user()->id;
            $warranty->save();
            activity('warranty')->causedBy(auth()->user())->performedOn($warranty)->log('updated');
            return response()->json(['status' => 1, 'message' => 'Warranty record restored successfully!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}
