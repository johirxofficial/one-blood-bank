<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailLog;
use Illuminate\Contracts\View\View;

class MailLogController extends Controller
{
    public function index(): View
    {
        return view('admin.logs.mail', [
            'logs' => MailLog::query()->with('user')->latest()->paginate(30),
        ]);
    }
}
