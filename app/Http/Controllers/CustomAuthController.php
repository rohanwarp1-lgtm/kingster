<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CustomAuthController extends Controller
{

    public function loginProcess(Request $request){

        $response = array();
        if (!Auth::attempt(['username' => str_replace(' ', '', $request->username), 'password' => $request->password])) {
            $response['status'] = 400;
            $response['message'] = "Username or password incorrect";
        } else {
            $user = User::where('username', $request->username)->first();
            if ($user->session_id != Session::getId()) {
                $user->session_id = Session::getId();
            }
            $user->save();

            $response['status'] = 200;
            $response['message'] = "Login successfull";
        }
		return response()->json($response)->header('Content-Type', 'application/json');
    }

    public function warrantyManagement(){
        $stats = [
            'total_warranties' => \App\Models\Warranty::count(),
            'pending_warranties' => \App\Models\Warranty::where('warranty_status', 'Pending')->count(),
            'active_warranties' => \App\Models\Warranty::where('warranty_status', 'Active')->count(),
            'total_products' => \App\Models\Product::count(),
        ];
        return view('admin/index_admin', compact('stats'));
    }

    public function signOut() {
        Session::flush();
        Auth::logout();

        return redirect()->route('login');
    }

    public function warrantyAjax(){

    }
}
