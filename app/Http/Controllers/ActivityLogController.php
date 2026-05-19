<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Activity::with('causer')->latest();

        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('event', $request->type);
        }

        $activities = $query->paginate(30);

        return view('admin.activity_log', compact('activities'));
    }
}
