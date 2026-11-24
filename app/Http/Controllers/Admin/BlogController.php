<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $term = $request->string('q')->toString();

        $posts = Post::query()
            ->with('author')
            ->when($term !== '', fn ($query) => $query->where('title', 'like', "%{$term}%")->orWhere('category', 'like', "%{$term}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.blog.index', [
            'posts' => $posts,
            'term' => $term,
        ]);
    }

    public function create(): View
    {
        return view('admin.blog.create', [
            'post' => new Post(['is_published' => true, 'published_at' => now()]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $data['user_id'] = $request->user()->id;
        $data['slug'] = filled($data['slug'] ?? null) ? $data['slug'] : Post::uniqueSlug($data['title']);
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $data['is_published'] ? ($data['published_at'] ?? now()) : null;
        $data['cover_path'] = $request->hasFile('cover') ? $request->file('cover')->store('blog', 'public') : null;

        $post = Post::query()->create($data);

        ActivityLog::record('blog.created', $post, 'ব্লগ পোস্ট তৈরি: '.$post->title);

        return redirect()->route('admin.blog.edit', $post)->with('success', 'ব্লগ পোস্ট তৈরি হয়েছে।');
    }

    public function edit(Post $post): View
    {
        return view('admin.blog.edit', [
            'post' => $post,
        ]);
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $data = $this->validated($request, $post);

        $data['slug'] = filled($data['slug'] ?? null) ? $data['slug'] : Post::uniqueSlug($data['title'], $post->id);
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $data['is_published'] ? ($data['published_at'] ?? $post->published_at ?? now()) : null;

        if ($request->hasFile('cover')) {
            if ($post->cover_path && Storage::disk('public')->exists($post->cover_path)) {
                Storage::disk('public')->delete($post->cover_path);
            }

            $data['cover_path'] = $request->file('cover')->store('blog', 'public');
        }

        $post->update($data);

        ActivityLog::record('blog.updated', $post, 'ব্লগ পোস্ট আপডেট: '.$post->title);

        return redirect()->route('admin.blog.edit', $post)->with('success', 'ব্লগ পোস্ট আপডেট হয়েছে।');
    }

    public function destroy(Post $post): RedirectResponse
    {
        if ($post->cover_path && Storage::disk('public')->exists($post->cover_path)) {
            Storage::disk('public')->delete($post->cover_path);
        }

        ActivityLog::record('blog.deleted', null, 'ব্লগ পোস্ট মুছে ফেলা: '.$post->title);

        $post->delete();

        return redirect()->route('admin.blog.index')->with('success', 'ব্লগ পোস্ট মুছে ফেলা হয়েছে।');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Post $post = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:200', 'alpha_dash', Rule::unique('posts', 'slug')->ignore($post?->id)],
            'category' => ['nullable', 'string', 'max:80'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string', 'min:20'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'meta_title' => ['nullable', 'string', 'max:200'],
            'meta_description' => ['nullable', 'string', 'max:300'],
            'published_at' => ['nullable', 'date'],
        ]);
    }
}
