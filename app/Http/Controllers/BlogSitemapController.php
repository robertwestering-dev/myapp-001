<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Response;

class BlogSitemapController extends Controller
{
    public function __invoke(): Response
    {
        $blogPosts = BlogPost::query()
            ->published()
            ->orderByDesc('published_at')
            ->get(['slug', 'updated_at', 'published_at']);

        return response()
            ->view('blog.sitemap', [
                'blogIndexUrl' => route('blog.index'),
                'blogPosts' => $blogPosts,
            ])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
