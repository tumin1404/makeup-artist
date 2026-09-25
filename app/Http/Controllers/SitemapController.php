<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Service;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Tạo XML Sitemap động cho toàn bộ website chuẩn SEO Google.
     */
    public function index(): Response
    {
        $baseUrl = url('/');
        $now = now()->toAtomString();

        $staticPages = [
            ['loc' => $baseUrl, 'priority' => '1.0', 'changefreq' => 'daily', 'lastmod' => $now],
            ['loc' => url('/services'), 'priority' => '0.9', 'changefreq' => 'weekly', 'lastmod' => $now],
            ['loc' => url('/portfolio'), 'priority' => '0.9', 'changefreq' => 'weekly', 'lastmod' => $now],
            ['loc' => url('/posts'), 'priority' => '0.8', 'changefreq' => 'daily', 'lastmod' => $now],
            ['loc' => url('/booking'), 'priority' => '0.8', 'changefreq' => 'monthly', 'lastmod' => $now],
            ['loc' => url('/gioi-thieu'), 'priority' => '0.7', 'changefreq' => 'monthly', 'lastmod' => $now],
        ];

        // Lấy tất cả bài viết đã xuất bản
        $posts = Post::where('status', 'published')
            ->orderByDesc('published_at')
            ->get();

        // Lấy tất cả dịch vụ đang hoạt động
        $services = Service::where('is_active', true)->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

        // 1. Static pages
        foreach ($staticPages as $page) {
            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars($page['loc']) . '</loc>';
            $xml .= '<lastmod>' . $page['lastmod'] . '</lastmod>';
            $xml .= '<changefreq>' . $page['changefreq'] . '</changefreq>';
            $xml .= '<priority>' . $page['priority'] . '</priority>';
            $xml .= '</url>';
        }

        // 2. Published Posts
        foreach ($posts as $post) {
            $postUrl = url('/posts/' . $post->slug);
            $lastmod = ($post->updated_at ?? $post->published_at ?? now())->toAtomString();

            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars($postUrl) . '</loc>';
            $xml .= '<lastmod>' . $lastmod . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.8</priority>';
            if (!empty($post->thumbnail)) {
                $imgUrl = asset('storage/' . $post->thumbnail);
                $xml .= '<image:image>';
                $xml .= '<image:loc>' . htmlspecialchars($imgUrl) . '</image:loc>';
                $xml .= '<image:title>' . htmlspecialchars($post->title) . '</image:title>';
                $xml .= '</image:image>';
            }
            $xml .= '</url>';
        }

        // 3. Active Services
        foreach ($services as $service) {
            $serviceUrl = url('/services/' . ($service->slug ?? $service->id));
            $lastmod = ($service->updated_at ?? now())->toAtomString();

            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars($serviceUrl) . '</loc>';
            $xml .= '<lastmod>' . $lastmod . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.8</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }
}
