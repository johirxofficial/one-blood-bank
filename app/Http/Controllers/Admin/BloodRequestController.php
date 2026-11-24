<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BloodRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BloodRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BloodRequestController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', Rule::enum(BloodRequestStatus::class)],
        ]);

        $requests = BloodRequest::query()
            ->with(['district', 'upazila', 'user'])
            ->when(filled($filters['status'] ?? null), fn ($query) => $query->where('status', $filters['status']))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.requests.index', compact('requests', 'filters'));
    }

    public function update(Request $request, BloodRequest $bloodRequest): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::enum(BloodRequestStatus::class)],
        ]);

        $bloodRequest->forceFill(['status' => $data['status']])->save();

        ActivityLog::record('request.updated', $bloodRequest, 'রক্তের অনুরোধ স্ট্যাটাস: '.$data['status']);

        return back()->with('success', 'অনুরোধ আপডেট হয়েছে।');
    }

    public function destroy(BloodRequest $bloodRequest): RedirectResponse
    {
        $bloodRequest->delete();

        ActivityLog::record('request.deleted', null, 'রক্তের অনুরোধ মুছে ফেলা হয়েছে।');

        return back()->with('success', 'অনুরোধ মুছে ফেলা হয়েছে।');
    }
}
