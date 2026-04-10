<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ $blogIndexUrl }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    @foreach ($blogPosts as $blogPost)
        <url>
            <loc>{{ $blogPost->publicUrl() }}</loc>
            <lastmod>{{ ($blogPost->updated_at ?? $blogPost->published_at)?->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.7</priority>
        </url>
    @endforeach
</urlset>
