<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App\Models\Warranty;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;



class UserController extends Controller
{
    public function index(){
        if (Auth::user()->role != 'Super Admin' && Auth::user()->role != 'CEO') {
            return redirect()->route('admin.warranty.management');
        }
        return view('admin.users');
    }

    public function save(Request $request){

        $isEdit = isset($request->user_id) && !empty($request->user_id);
        $changePassword = $request->has('change_password') && $request->change_password;
        $isCEOEditingSelf = $isEdit && $request->user_id == 1; // CEO editing their own profile

        $rules = [
            'user_name'     => 'required|string|max:255|unique:users,username' . ($isEdit ? ',' . $request->user_id : ''),
            'user_email'    => 'required|email|unique:users,email' . ($isEdit ? ',' . $request->user_id : ''),
        ];

        // Skip role validation for CEO editing their own profile
        if (!$isCEOEditingSelf) {
            $rules['user_role'] = 'required|string';
        }

        if (!$isEdit || $changePassword) {
            $rules['user_password'] = 'required|string|min:6';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        if($isEdit){
            $user = User::find($request->user_id);
        }else{
            $user = new User();
        }
        $user->username = $request->user_name;
        $user->email = $request->user_email;

        // If CEO is editing their own profile, ensure role remains CEO
        if ($isCEOEditingSelf) {
            $user->role = 'CEO';
        } else {
            $user->role = $request->user_role;
        }

        if (!$isEdit || $changePassword) {
            $user->password = Hash::make($request->user_password);
        }
        $user->save();

        $action = $isEdit ? 'updated' : 'created';
        activity('user')->causedBy(auth()->user())->performedOn($user)->withProperties(['username' => $user->username, 'role' => $user->role])->log($action);

        return response()->json([
            'status' => true,
            'message' => $isEdit ? 'User updated successfully!' : 'User created successfully!',
            'id' => $user->id,
        ]);
    }

    public function ajax(Request $request){
        $searchColumns = [
            'id',
            'username',
            'email',
            'role',
        ];

        $sortingColumns = [
            0 => 'id',
            1 => 'username',
            2 => 'email',
            3 => 'role',
        ];

        $query = User::query();

        // Filter by status (deleted/active)
        if (isset($request->status_filter)) {
            $query->where('is_deleted', $request->status_filter);
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

        $recordsTotal = User::count();
        $recordsFiltered = $query->count();

        // Pagination
        $query->skip($request->start)->take($request->length != -1 ? $request->length : $recordsTotal);

        $data = $query->get();

        // Format data
        $viewData = [];
        foreach ($data as $item) {
            $row = [];

            // Action column
            $uiAction = '<ul class="list-inline font-size-20 contact-links mb-0">';

            if($item->is_deleted == 0){
                if($item->id != 1){
                    if($item->id != Auth::user()->id){
                        $uiAction .= '<li class="list-inline-item px-2 pos-middle"><a href="javascript:void(0);" onclick="userDelete(' . $item->id . ')"><i class="fe fe-trash"></i></a></li>';
                    }
                    $uiAction .= '<li class="list-inline-item px-2 pos-middle"><a href="javascript:void(0);" onclick="editUserCredentials(' . $item->id . ')"><i class="fe fe-edit"></i></a></li>';
                }

                if(Auth::user()->id == 1 && $item->id == 1){
                    $uiAction .= '<li class="list-inline-item px-2 pos-middle"><a href="javascript:void(0);" onclick="editUserCredentials(' . $item->id . ')"><i class="fe fe-edit"></i></a></li>';
                }
            }else{
                if($item->id != 1){
                    $uiAction .= '<li class="list-inline-item px-2 pos-middle"><a href="javascript:void(0);" onclick="userRestore(' . $item->id . ')"><i class="fe fe-rotate-ccw"></i></a></li>';
                }
            }

            $uiAction .= '</ul>';
            $row['action'] = $uiAction;
            $row['username'] = $item->username;
            $row['email'] = $item->email;
            $row['role'] = ucfirst($item->role);

            $viewData[] = $row;
        }

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $viewData,
        ]);
    }

    public function delete(Request $request){
        try {
            $user = User::find($request->id);
            if($user){
                if($user->id == 1){
                    return response()->json(['status' => false, 'message' => 'CEO cannot be deleted!']);
                }
                $user->is_deleted = 1;
                $user->save();
                activity('user')->causedBy(auth()->user())->performedOn($user)->log('deleted');
                return response()->json(['status' => 1, 'message' => 'User deleted successfully!']);
            }
            return response()->json(['status' => false, 'message' => 'User not found!']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }

    public function restore(Request $request){
        try {
            $user = User::find($request->id);
            if($user){
                $user->is_deleted = 0;
                $user->save();
                activity('user')->causedBy(auth()->user())->performedOn($user)->log('updated');
                return response()->json(['status' => 1, 'message' => 'User restored successfully!']);
            }
            return response()->json(['status' => false, 'message' => 'User not found!']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }

    public function getDetails(Request $request){
        $user = User::find($request->id);
        if($user){
            return response()->json([
                'status' => true,
                'data' => [
                    'id'    => $user->id,
                    'name'  => $user->username,
                    'email' => $user->email,
                    'role'  => $user->role
                ]
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'User not found'
        ]);
    }
}
