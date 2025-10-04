<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        $posts = Post::query()
            ->published()
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(9);

        return view('posts.index', compact('posts'));
    }

    public function show(Post $post): View
    {
        abort_unless($post->is_published, 404);

        $recentPosts = Post::query()
            ->published()
            ->whereKeyNot($post->getKey())
            ->latest('published_at')
            ->limit(4)
            ->get();

        return view('posts.show', [
            'post' => $post,
            'recentPosts' => $recentPosts,
        ]);
    }
}
