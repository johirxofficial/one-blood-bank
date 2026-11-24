<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless((bool) Setting::get('others.blog_enabled', true), 404);

        $category = $request->string('category')->toString();

        $posts = Post::query()
            ->published()
            ->with('author')
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->latest('published_at')
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('blog.index', [
            'posts' => $posts,
            'category' => $category,
            'categories' => Post::query()
                ->published()
                ->whereNotNull('category')
                ->select('category')
                ->distinct()
                ->orderBy('category')
                ->pluck('category'),
        ]);
    }

    public function show(string $slug): View
    {
        abort_unless((bool) Setting::get('others.blog_enabled', true), 404);

        $post = Post::query()
            ->published()
            ->with('author')
            ->where('slug', $slug)
            ->firstOrFail();

        $post->increment('views');

        $related = Post::query()
            ->published()
            ->whereKeyNot($post->id)
            ->when($post->category, fn ($query) => $query->where('category', $post->category))
            ->latest('published_at')
            ->take(3)
            ->get();

        if ($related->isEmpty()) {
            $related = Post::query()->published()->whereKeyNot($post->id)->latest('published_at')->take(3)->get();
        }

        return view('blog.show', [
            'post' => $post,
            'related' => $related,
        ]);
    }
}
