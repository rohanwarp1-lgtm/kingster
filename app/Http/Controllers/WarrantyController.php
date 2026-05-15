<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use Session;
use App\Models\Warranty;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class WarrantyController extends Controller
{
    public function save(Request $request){

        Warranty::whereIn('warranty_status', ['Active', 'Pending'])->whereDate('expiry_date', '<', Carbon::today())->update(['warranty_status' => 'Expired']);

        if($request->type == 'status'){
            $serial = $request->serial_number;
            $mobile = $request->mobile_number;

            if (empty($serial) && empty($mobile)) {
                return response()->json([
                    'message' => 'Please provide either a serial number or mobile number.',
                    'status' => 4001,
                    'message' => 'Validation failed.',
                    'data' => []
                ]);
            }

            $query = Warranty::where('is_deleted', 0);
            if (!empty($serial)) {
                $query->where('serial_number', $serial);
            }

            if (!empty($mobile)) {
                $query->where('mobile_number', $mobile);
            }

            if (!empty($serial) && !empty($mobile)) {
                $query->where('serial_number', $serial)->where('mobile_number', $mobile);
            }

            $warranties = $query->get();

            if ($warranties->isEmpty()) {
                return response()->json([
                    'message' => 'No warranty records found.',
                    'status' => 4333,
                    'message' => 'Record not found.',
                    'data' => []
                ]);
            }

            $responseData = [];

            foreach ($warranties as $warranty) {
                $expiry = $warranty->expiry_date ? Carbon::parse($warranty->expiry_date) : null;

                if ($expiry && $expiry->isPast() && $warranty->warranty_status === 'Active') {
                    $warranty->warranty_status = 'Expired';
                    $warranty->modified_by = 1;
                    $warranty->save();
                }

                $responseData[] = [
                    'buyer_name'      => $warranty->user_name,
                    'mobile_number'   => $warranty->mobile_number,
                    'email'           => $warranty->email,
                    'purchase_source' => $warranty->purchase_source,
                    'product_name'    => $warranty->product_name,
                    'serial_number'   => $warranty->serial_number,
                    'purchase_date'   => $warranty->purchase_date
                        ? Carbon::createFromFormat('Y-m-d', $warranty->purchase_date)->format('d-m-Y')
                        : '',
                    'expiry_date'     => $warranty->expiry_date
                        ? Carbon::createFromFormat('Y-m-d', $warranty->expiry_date)->format('d-m-Y')
                        : '',
                    'warranty_status' => $warranty->warranty_status,
                ];
            }
            
            $supportMobile = DB::table('general_settings')->value('customer_support_mobile');


            if (count($responseData) === 1) {
                $status = $warranties[0]->warranty_status;

                switch ($status) {
                    case 'Pending':
                        return response()->json([
                            'message' => "The warranty for Serial Number {$warranties[0]->serial_number} is already submitted and is currently pending verification.",
                            'status' => 4009,
                            'message' => 'Warranty is pending!',
                            'data' => $responseData[0]
                        ]);

                    case 'Active':
                        return response()->json([
                            'message' => "The warranty for Serial Number {$warranties[0]->serial_number} is already active. No further action needed.",
                            'status' => 2000,
                            'message' => 'Warranty active!',
                            'data' => $responseData[0]
                        ]);

                    case 'Expired':
                        return response()->json([
                            'message' => "The warranty for Serial Number {$warranties[0]->serial_number} has expired.",
                            'status' => 4100,
                            'message' => 'Warranty expired!',
                            'data' => $responseData[0]
                        ]);
                        

                    case 'Rejected':
                        return response()->json([
                            'message' => "Warranty application for Serial Number {$warranties[0]->serial_number} is rejected. Contact support: {$supportMobile}",
                            'status' => 4003,
                            'message' => 'Warranty rejected!',
                            'data' => $responseData[0]
                        ]);

                    default:
                        return response()->json([
                            'message' => "RECORD FOUND",
                            'status' => 2001,
                            'message' => 'Record found with unknown status.',
                            'data' => $responseData[0]
                        ]);
                }
            }

            return response()->json([
                'message' => count($responseData) . ' matching warranty records found.',
                'status' => 2002,
                'message' => 'Multiple records found.',
                'data' => $responseData
            ]);

        }

        // if($request->type == 'application'){
        //     $purchase_date = $request->purchase_date;
        //     $purchaseDate = Carbon::parse(str_replace('/', '-', $purchase_date));

        //     $purchaseDate = Carbon::createFromFormat('Y-m-d', $purchase_date);
        //     $expiryDate = $purchaseDate->addDays(365);

        //     $findData = Warranty::where('serial_number', $request->serial_number)->where('is_deleted', 0)->where('warranty_status', '!=', 'Rejected')->first();
        //     if(isset($findData)){
        //         return response()->json([
        //             'message' => "Serial Number ".$request->serial_number." already exists.",
        //             'status' => 400,
        //             'message' => 'Warranty saved successfully.'
        //         ]);
        //     }

        //     $warranty = new Warranty();
        //     $warranty->user_name = $request->buyer_name;
        //     $warranty->mobile_number = $request->mobile;
        //     $warranty->email = $request->email;
        //     $warranty->purchase_source = $request->purchase_source;
        //     $warranty->address = $request->address;
        //     $warranty->product_name = $request->product_name;
        //     $warranty->serial_number = $request->serial_number;
        //     $warranty->purchase_date = Carbon::createFromFormat('Y-m-d', $purchase_date);
        //     $warranty->expiry_date = $expiryDate->format('Y-m-d');
        //     $warranty->warranty_status = 'Pending';
        //     $warranty->modified_by = Auth::user()->id;
        //     $warranty->save();

        //     return response()->json([
        //         'message' => "",
        //         'status' => 200,
        //         'message' => 'Warranty saved successfully.'
        //     ]);
        // }
        
       if($request->type == 'application'){
            $purchase_date = $request->purchase_date;

            try {
                $purchaseDate = Carbon::parse(str_replace('/', '-', $purchase_date));
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Invalid purchase date format.',
                    'status' => 4002,
                ]);
            }

            $expiryDate = $purchaseDate->copy()->addDays(365);

            $findData = Warranty::where('serial_number', $request->serial_number)->where('is_deleted', 0)->where('warranty_status', '!=', 'Rejected')->first();
            if(isset($findData)){
                return response()->json([
                    'message' => "Serial Number ".$request->serial_number." already exists.",
                    'status' => 400,
                    'message' => 'Warranty saved successfully.'
                ]);
            }

            $warranty = new Warranty();
            $warranty->user_name = $request->buyer_name;
            $warranty->mobile_number = $request->mobile;
            $warranty->email = $request->email;
            $warranty->purchase_source = $request->purchase_source;
            $warranty->address = $request->address;
            $warranty->product_name = $request->product_name;
            $warranty->serial_number = $request->serial_number;
            $warranty->purchase_date = $purchaseDate->format('Y-m-d');
            $warranty->expiry_date = $expiryDate->format('Y-m-d');
            $warranty->warranty_status = 'Pending';
            $warranty->modified_by = Auth::check() ? Auth::user()->id : 1;
            $warranty->save();

            return response()->json([
                'message' => "",
                'status' => 200,
                'message' => 'Warranty saved successfully.'
            ]);
        }


    }

    public function ajax(Request $request) {
        $searchColumns = [
            'warranty_records.user_name',
            'warranty_records.mobile_number',
            'warranty_records.email',
            'warranty_records.purchase_source',
            'warranty_records.product_name',
            'warranty_records.serial_number',
            DB::raw("DATE_FORMAT(warranty_records.purchase_date, '%d-%m-%Y')"),
            DB::raw("DATE_FORMAT(warranty_records.expiry_date, '%d-%m-%Y')"),
            'warranty_records.warranty_status',
            'warranty_records.address',
            'u1.username',
            'u2.username',
        ];

        $sortingColumns = [
            0 => 'warranty_records.id', // Action column - not sortable but included for index match
            1 => 'warranty_records.warranty_status',
            2 => 'warranty_records.user_name',
            3 => 'warranty_records.mobile_number',
            4 => 'warranty_records.email',
            5 => 'warranty_records.purchase_source',
            6 => 'warranty_records.product_name',
            7 => 'warranty_records.serial_number',
            8 => 'warranty_records.purchase_date',
            9 => 'warranty_records.expiry_date',
            10 => 'warranty_records.address',
            11 => 'u1.username',
            12 => 'u2.username',
        ];

        $selectColumns = [
            'warranty_records.id',
            'warranty_records.user_name',
            'warranty_records.mobile_number',
            'warranty_records.email',
            'warranty_records.purchase_source',
            'warranty_records.product_name',
            'warranty_records.serial_number',
            'warranty_records.purchase_date',
            'warranty_records.expiry_date',
            'warranty_records.warranty_status',
            'warranty_records.address',
            'warranty_records.is_deleted',
            'u1.username as created_by',
			'u2.username as modified_by',
        ];

        $recordsTotal = Warranty::where('warranty_records.is_deleted', 0)->count();

        $query = Warranty::query();
        $query->select($selectColumns);
		$query->leftJoin('users as u1', 'u1.id', '=', 'warranty_records.created_by');
		$query->leftJoin('users as u2', 'u2.id', '=', 'warranty_records.modified_by');

        if(isset($request->warranty_status) && $request->warranty_status != 'All'){
            $query->where('warranty_records.warranty_status', $request->warranty_status);
        }

        if(isset($request->status_filter)){
            $query->where('warranty_records.is_deleted', $request->status_filter);
        }

        // Sorting
        if (isset($request['order'][0])) {
            $query->orderBy(
                $sortingColumns[$request['order'][0]['column']],
                $request['order'][0]['dir']
            );
        }

        // Search
        if (!empty($request['search']['value'])) {
            $search = $request['search']['value'];
            $query->where(function ($q) use ($search, $searchColumns) {
                foreach ($searchColumns as $i => $column) {
                    $i === 0 ? $q->where($column, 'like', "%$search%")
                            : $q->orWhere($column, 'like', "%$search%");
                }
            });
        }

        $recordsFiltered = $query->count();

        // Pagination
        $query->skip($request->start)->take($request->length != -1 ? $request->length : $recordsTotal);
        $data = $query->get();

        Warranty::whereIn('warranty_status', ['Active', 'Pending'])->whereDate('expiry_date', '<', Carbon::today())->update(['warranty_status' => 'Expired']);

        // Data Formatting
        $viewData = [];
        foreach ($data as $item) {
            $row = [];

			$uiAction = '<ul class="list-inline font-size-20 contact-links mb-0">';

            if($item->is_deleted == 0){
                $uiAction .= '<li class="list-inline-item px-2 pos-middle"><a href="javascript:void(0);" onclick="userDelete(' . $item->id . ')"><i class="fe fe-trash"></i></a></li>';
            }else{
                $uiAction .= '<li class="list-inline-item px-2 pos-middle"><a href="javascript:void(0);" onclick="userRestore(' . $item->id . ')"><i class="fe fe-rotate-ccw"></i></a></li>';
            }

            $uiAction .= '<li class="list-inline-item px-2 pos-middle"><a href="javascript:void(0);" onclick="changeWarrantyStatus(' . $item->id . ', \'' . $item->warranty_status . '\')"><i class="fe fe-sliders"></i></a></li>';
			$uiAction .= '</ul>';
			$row['action'] = $uiAction;

            $status = ucfirst($item->warranty_status);

            $colorClass = match ($item->warranty_status) {
                'Pending'  => 'text-warning',
                'Active'   => 'text-success',
                'Expired'  => 'text-danger',
                'Rejected' => 'text-muted',
            };

            $row['warranty_status'] = '<span class="' . $colorClass . '">' . $status . '</span>';

            $row['buyer_name'] = $item->user_name;
            $row['mobile'] = $item->mobile_number;
            $row['email'] = $item->email;
            $row['source'] = $item->purchase_source;
            $row['product_name'] = $item->product_name;
            $row['serial_number'] = $item->serial_number;
            $row['address'] = $item->address;
            $row['purchase_date'] = date('d-m-Y', strtotime($item->purchase_date));
            $row['expiry_date'] = date('d-m-Y', strtotime($item->expiry_date));
            $row['modified_by'] = $item->modified_by;
            $row['created_by'] = $item->created_by;

            $viewData[] = $row;
        }

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $viewData,
        ]);
    }

    public function changeStatus(Request $request){
        try {
            $warranty = Warranty::findOrFail($request->id);
            $warranty->warranty_status = $request->status;
            $warranty->modified_by = Auth::user()->id;
            $warranty->save();

            return response()->json([
                'status' => 1,
                'message' => 'Warranty status updated.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ]);
        }
    }

    public function delete(Request $request){

        $findData = Warranty::find($request->id);
        $findData->is_deleted = 1;
        $findData->modified_by = Auth::user()->id;
        $findData->save();

        return response()->json([
            'status' => 1,
            'message' => 'Warranty deleted successfully.'
        ]);
    }

    public function restore(Request $request){

        $findData = Warranty::find($request->id);
        $findData->is_deleted = 0;
        $findData->modified_by = Auth::user()->id;
        $findData->save();

        return response()->json([
            'status' => 1,
            'message' => 'Warranty restore successfully.'
        ]);
    }
}
