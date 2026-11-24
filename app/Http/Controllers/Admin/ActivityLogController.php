<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Contracts\View\View;

class ActivityLogController extends Controller
{
    public function index(): View
    {
        return view('admin.logs.activity', [
            'logs' => ActivityLog::query()->with('user')->latest('created_at')->paginate(30),
        ]);
    }
}
