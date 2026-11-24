<?php

namespace App\Http\Controllers;

use App\Enums\BloodGroup;
use App\Enums\BloodRequestStatus;
use App\Models\BloodRequest;
use App\Models\District;
use App\Models\Setting;
use App\Rules\PhoneNumber;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BloodRequestController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'blood_group' => ['nullable', Rule::enum(BloodGroup::class)],
            'district_id' => ['nullable', 'integer', 'exists:districts,id'],
        ]);

        $requests = BloodRequest::query()
            ->open()
            ->with(['district', 'upazila'])
            ->when(filled($filters['blood_group'] ?? null), fn ($query) => $query->where('blood_group', $filters['blood_group']))
            ->when(filled($filters['district_id'] ?? null), fn ($query) => $query->where('district_id', $filters['district_id']))
            ->orderBy('needed_at')
            ->paginate(12)
            ->withQueryString();

        return view('requests.index', [
            'requests' => $requests,
            'filters' => $filters,
            'districts' => District::query()->forCountry()->orderBy('name')->get(),
            'canSeePhone' => $request->user() !== null
                && (! Setting::phoneVerificationRequired() || $request->user()->phone_verified_at !== null),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['contact_phone' => PhoneNumber::normalize($request->input('contact_phone'))]);

        $data = $request->validate([
            'patient_name' => ['required', 'string', 'max:120'],
            'blood_group' => ['required', Rule::enum(BloodGroup::class)],
            'units' => ['required', 'integer', 'between:1,10'],
            'hospital' => ['required', 'string', 'max:160'],
            'district_id' => ['nullable', 'integer', 'exists:districts,id'],
            'upazila_id' => ['nullable', 'integer', 'exists:upazilas,id'],
            'contact_phone' => ['required', 'string', 'max:20', new PhoneNumber],
            'needed_at' => ['required', 'date', 'after:now'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        BloodRequest::query()->create($data + [
            'user_id' => $request->user()->id,
            'status' => BloodRequestStatus::Open,
        ]);

        return back()->with('success', 'আপনার রক্তের অনুরোধ পোস্ট হয়েছে।');
    }
}
