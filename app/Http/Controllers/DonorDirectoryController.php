<?php

namespace App\Http\Controllers;

use App\Enums\BloodGroup;
use App\Models\District;
use App\Models\Division;
use App\Models\Donor;
use App\Models\Setting;
use App\Models\Union;
use App\Models\Upazila;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DonorDirectoryController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ($request->user() === null && (bool) Setting::get('donors.require_login', true)) {
            session()->put('url.intended', $request->fullUrl());

            return redirect()->route('login')
                ->with('warning', 'ডোনারদের তথ্য দেখতে প্রথমে লগইন করুন — এতে ডোনারদের নম্বর সুরক্ষিত থাকে।');
        }

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:60'],
            'blood_group' => ['nullable', Rule::enum(BloodGroup::class)],
            'country' => ['nullable', 'string', 'max:80'],
            'state' => ['nullable', 'string', 'max:80'],
            'city' => ['nullable', 'string', 'max:80'],
            'division_id' => ['nullable', 'integer', 'exists:divisions,id'],
            'district_id' => ['nullable', 'integer', 'exists:districts,id'],
            'upazila_id' => ['nullable', 'integer', 'exists:upazilas,id'],
            'union_id' => ['nullable', 'integer', 'exists:unions,id'],
            'available' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'in:rating,recent,newest'],
        ]);

        $donors = Donor::query()
            ->approved()
            ->with(['user', 'division', 'district', 'upazila', 'union'])
            ->filter($filters)
            ->orderForDirectory($filters['sort'] ?? null)
            ->paginate(12)
            ->withQueryString();

        $divisionId = $filters['division_id'] ?? null;
        $districtId = $filters['district_id'] ?? null;
        $upazilaId = $filters['upazila_id'] ?? null;

        return view('donors.index', [
            'donors' => $donors,
            'filters' => $filters,
            'divisions' => Division::query()->forCountry()->orderBy('name')->get(),
            'districts' => $divisionId ? District::query()->forCountry()->where('division_id', $divisionId)->orderBy('name')->get() : collect(),
            'upazilas' => $districtId ? Upazila::query()->forCountry()->where('district_id', $districtId)->orderBy('name')->get() : collect(),
            'unions' => $upazilaId ? Union::query()->forCountry()->where('upazila_id', $upazilaId)->orderBy('name')->get() : collect(),
            'countries' => Donor::query()->approved()->whereNotNull('country')->where('country', '!=', '')->select('country')->distinct()->orderBy('country')->pluck('country'),
        ]);
    }

    public function show(Request $request, string $username): View|RedirectResponse
    {
        if ($request->user() === null && (bool) Setting::get('donors.require_login', true)) {
            session()->put('url.intended', $request->fullUrl());

            return redirect()->route('login')
                ->with('warning', 'ডোনারের প্রোফাইল দেখতে লগইন করুন।');
        }

        $donor = Donor::query()
            ->approved()
            ->with([
                'user', 'division', 'district', 'upazila', 'union', 'contacts',
                'donations' => fn ($query) => $query->latest('donated_at')->limit(5),
                'ratings' => fn ($query) => $query->published()->with('user')->latest()->limit(10),
            ])
            ->whereHas('user', fn ($query) => $query->where('username', $username))
            ->firstOrFail();

        $userRating = $request->user()
            ? $donor->ratings()->where('user_id', $request->user()->id)->value('rating')
            : null;

        return view('donors.show', [
            'donor' => $donor,
            'userRating' => $userRating,
        ]);
    }
}
