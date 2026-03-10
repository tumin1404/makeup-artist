<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        // Lấy bài viết mới nhất và được đánh dấu nổi bật
        $featuredPost = \App\Models\Post::where('status', 'published')
            ->where('is_featured', true)
            ->latest('published_at')
            ->first();

        // Lấy các bài viết còn lại (không bao gồm bài nổi bật trên)
        $posts = \App\Models\Post::where('status', 'published')
            ->when($featuredPost, function ($query) use ($featuredPost) {
                return $query->where('id', '!=', $featuredPost->id);
            })
            ->latest('published_at')
            ->paginate(9);

        // Lấy danh mục bài viết
        $categories = \App\Models\Category::all();

        return view('posts.index', compact('featuredPost', 'posts', 'categories'));
    }
    public function show($slug)
    {
        // Tìm bài viết theo slug, nếu không thấy trả về 404
        $post = \App\Models\Post::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();
        
        // Lấy 3 bài viết liên quan (cùng danh mục) để hiển thị ở cuối trang
        $relatedPosts = \App\Models\Post::where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->where('status', 'published')
            ->latest()
            ->take(3)
            ->get();

        return view('posts.show', compact('post', 'relatedPosts'));
    }
}
