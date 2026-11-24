<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AdPlacement;
use App\Enums\AdType;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Ad;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdController extends Controller
{
    public function index(): View
    {
        return view('admin.ads.index', [
            'ads' => Ad::query()->orderBy('placement')->orderBy('sort_order')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.ads.form', [
            'ad' => new Ad(['is_active' => true, 'sort_order' => 0]),
            'types' => AdType::cases(),
            'placements' => AdPlacement::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        Ad::query()->create($data);

        ActivityLog::record('ad.created', null, 'নতুন বিজ্ঞাপন: '.$data['title']);

        return redirect()->route('admin.ads.index')->with('success', 'বিজ্ঞাপন যোগ হয়েছে।');
    }

    public function edit(Ad $ad): View
    {
        return view('admin.ads.form', [
            'ad' => $ad,
            'types' => AdType::cases(),
            'placements' => AdPlacement::cases(),
        ]);
    }

    public function update(Request $request, Ad $ad): RedirectResponse
    {
        $data = $this->validated($request);

        $ad->update($data);

        ActivityLog::record('ad.updated', $ad, 'বিজ্ঞাপন আপডেট: '.$ad->title);

        return redirect()->route('admin.ads.index')->with('success', 'বিজ্ঞাপন আপডেট হয়েছে।');
    }

    public function destroy(Ad $ad): RedirectResponse
    {
        $ad->delete();

        ActivityLog::record('ad.deleted', null, 'বিজ্ঞাপন মুছে ফেলা হয়েছে।');

        return back()->with('success', 'বিজ্ঞাপন মুছে ফেলা হয়েছে।');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'type' => ['required', Rule::enum(AdType::class)],
            'placement' => ['required', Rule::enum(AdPlacement::class)],
            'target_url' => ['nullable', 'url', 'max:255'],
            'code' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('ads', 'public');
        }

        unset($data['image']);

        return $data;
    }
}
