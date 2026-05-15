<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use Session;
use App\Models\Warranty;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\GeneralSetting;


class GeneralSettingController extends Controller
{
    public function index(){
        $setting = \App\Models\GeneralSetting::first();
        return view('admin/general_setting', compact('setting'));
    }

    public function save(Request $request){
        $validated = $request->validate([
            'brand_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'brand_white_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'brand_fevicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico|max:2048',
            'header_banner_1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'header_banner_2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'header_banner_3' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'header_banner_4' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'customer_support_mobile' => 'nullable|string|max:255',
            'customer_support_email' => 'nullable|email|max:255',
            'office_time' => 'nullable|string|max:255',
            'footer_about_us_1' => 'nullable|string',
            'footer_about_us_2' => 'nullable|string',
            'ig_link' => 'nullable|string|max:255',
            'wp_link' => 'nullable|string|max:255',
            'footer_copyright' => 'nullable|string|max:255',
            'active_clients' => 'nullable|integer',
            'expert_mechanics' => 'nullable|integer',
            'reputation_years' => 'nullable|integer',
            'first_reviewer_name' => 'nullable|string|max:255',
            'first_reviewer_msg' => 'nullable|string',
            'second_reviewer_name' => 'nullable|string|max:255',
            'second_reviewer_msg' => 'nullable|string',
            'third_reviewer_name' => 'nullable|string|max:255',
            'third_reviewer_msg' => 'nullable|string',
        ]);

        $setting = GeneralSetting::first() ?? new GeneralSetting();

        $fieldsWithFiles = [
            'brand_logo', 'brand_white_logo', 'brand_fevicon',
            'header_banner_1', 'header_banner_2',
            'header_banner_3', 'header_banner_4'
        ];
        foreach ($fieldsWithFiles as $field) {
            if ($request->hasFile($field)) {
                $image = $request->file($field);
                $uniqueName = time() . '_' . mt_rand(100000, 999999);
                $imageName = $uniqueName . "_general_setting_" . $image->getClientOriginalName();
                $image->move(base_path('public/uploads/general_settings'), $imageName);
                $setting->$field = 'uploads/general_settings/' . $imageName;
            }
        }

        $setting->customer_support_mobile = $request->customer_support_mobile;
        $setting->customer_support_email = $request->customer_support_email;
        $setting->office_time = $request->office_time;
        $setting->footer_about_us_1 = $request->footer_about_us_1;
        $setting->footer_about_us_2 = $request->footer_about_us_2;
        $setting->ig_link = $request->ig_link;
        $setting->wp_link = $request->wp_link;
        $setting->footer_copyright = $request->footer_copyright;
        $setting->active_clients = isset($request->active_clients) ? $request->active_clients : 212089;
        $setting->expert_mechanics = isset($request->expert_mechanics) ? $request->expert_mechanics : 10;
        $setting->reputation_years = isset($request->reputation_years) ? $request->reputation_years : 7;
        $setting->first_reviewer_name = $request->first_reviewer_name;
        $setting->first_reviewer_msg = $request->first_reviewer_msg;
        $setting->second_reviewer_name = $request->second_reviewer_name;
        $setting->second_reviewer_msg = $request->second_reviewer_msg;
        $setting->third_reviewer_name = $request->third_reviewer_name;
        $setting->third_reviewer_msg = $request->third_reviewer_msg;

        $setting->modified_by = auth()->id() ?? 1;

        $setting->save();

        return response()->json(['success' => true, 'message' => 'Settings saved successfully!']);
    }

}
