<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    public function index(): View
    {
        return view('admin.pages.index', [
            'pages' => Page::query()->orderBy('slug')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.pages.form', ['page' => new Page(['is_published' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        Page::query()->create($data);

        ActivityLog::record('page.created', null, 'নতুন পেজ: '.$data['title']);

        return redirect()->route('admin.pages.index')->with('success', 'পেজ যোগ হয়েছে।');
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.form', compact('page'));
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $page->update($this->validated($request, $page));

        ActivityLog::record('page.updated', $page, 'পেজ আপডেট: '.$page->title);

        return redirect()->route('admin.pages.index')->with('success', 'পেজ আপডেট হয়েছে।');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();

        ActivityLog::record('page.deleted', null, 'পেজ মুছে ফেলা হয়েছে।');

        return back()->with('success', 'পেজ মুছে ফেলা হয়েছে।');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Page $page = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'slug' => ['required', 'string', 'max:100', 'alpha_dash', Rule::unique('pages', 'slug')->ignore($page?->id)],
            'content' => ['required', 'string'],
            'meta_title' => ['nullable', 'string', 'max:180'],
            'meta_description' => ['nullable', 'string', 'max:300'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = Str::slug($data['slug']);
        $data['is_published'] = $request->boolean('is_published');

        return $data;
    }
}
