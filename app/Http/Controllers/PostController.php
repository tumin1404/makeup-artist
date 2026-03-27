<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Post;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $categorySlug = $request->query('category');

        // 1. Chỉ lấy các danh mục thuộc loại bài viết (Tạp chí)
        $categories = Category::where('type', Category::TYPE_POST)->orderBy('order')->get();

        // 2. Query cơ bản kèm relationship để tối ưu N+1
        $query = Post::with('category')->where('status', 'published');

        // 3. Lọc bài viết nếu URL có chứa ?category=slug
        if ($categorySlug) {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // 4. Lấy bài viết nổi bật (theo query đã lọc)
        $featuredPost = (clone $query)->where('is_featured', true)->latest('published_at')->first();

        // 5. Lấy danh sách bài viết còn lại phân trang
        $posts = (clone $query)->when($featuredPost, function ($q) use ($featuredPost) {
                return $q->where('id', '!=', $featuredPost->id);
            })
            ->latest('published_at')
            ->paginate(9);

        return view('posts.index', compact('featuredPost', 'posts', 'categories'));
    }

    public function show($slug)
    {
        $post = Post::with('category')->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();
        
        $relatedPosts = Post::where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->where('status', 'published')
            ->latest()
            ->take(3)
            ->get();

        return view('posts.show', compact('post', 'relatedPosts'));
    }
}
