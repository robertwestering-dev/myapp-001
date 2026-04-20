<?php

use App\Models\BlogPost;
use App\Models\MediaAsset;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

test('guests can visit the public blog index and only see published posts', function () {
    $publishedPost = BlogPost::factory()->featured()->create([
        'title' => [
            'nl' => 'Publieke blogpost',
            'en' => 'Public blog post',
            'de' => 'Offentlicher Blogbeitrag',
        ],
    ]);

    BlogPost::factory()->draft()->create([
        'title' => [
            'nl' => 'Concept blogpost',
            'en' => 'Draft blog post',
            'de' => 'Entwurf Blogbeitrag',
        ],
    ]);

    $response = $this->get(route('blog.index'));

    $response->assertOk()
        ->assertSee(__('hermes.blog.summary_title'))
        ->assertSee(__('hermes.blog.editorial_note'))
        ->assertSee($publishedPost->titleForLocale())
        ->assertSee('user-section-heading', false)
        ->assertSee('user-filter-panel', false)
        ->assertSee('user-inline-meta', false)
        ->assertSee('user-surface-card', false)
        ->assertSee('blog-content', false)
        ->assertSee('blog-article-card', false)
        ->assertSee('blog-sidebar', false)
        ->assertDontSee('Concept blogpost');
});

test('visitors can filter the public blog on tag', function () {
    BlogPost::factory()->create([
        'tags' => ['AI adoptie', 'Leiderschap'],
        'title' => [
            'nl' => 'AI artikel',
            'en' => 'AI article',
            'de' => 'AI Artikel',
        ],
    ]);

    BlogPost::factory()->create([
        'tags' => ['Teamontwikkeling'],
        'title' => [
            'nl' => 'Team artikel',
            'en' => 'Team article',
            'de' => 'Team Artikel',
        ],
    ]);

    $response = $this->get(route('blog.index', ['tag' => 'AI adoptie']));

    $response->assertOk()
        ->assertSee('AI artikel')
        ->assertDontSee('Team artikel');
});

test('public blog rebuilds invalid cached tag counts', function () {
    Cache::put('blog:tag_counts', 'stale-invalid-value');

    BlogPost::factory()->create([
        'tags' => ['Digitale weerbaarheid'],
        'title' => [
            'nl' => 'Artikel met tag',
            'en' => 'Article with tag',
            'de' => 'Artikel mit Tag',
        ],
    ]);

    $this->get(route('blog.index'))
        ->assertOk()
        ->assertSee('Digitale weerbaarheid');

    expect(Cache::get('blog:tag_counts'))->toBeArray();
});

test('authenticated users see the same primary menu on the blog as on other user pages', function () {
    $user = User::factory()->create();
    $blogPost = BlogPost::factory()->create([
        'title' => [
            'nl' => 'Ingelogde blogpost',
            'en' => 'Authenticated blog post',
            'de' => 'Angemeldeter Blogbeitrag',
        ],
    ]);

    $this->actingAs($user)
        ->get(route('blog.show', $blogPost))
        ->assertOk()
        ->assertSee(route('dashboard', absolute: false), false)
        ->assertSee(route('questionnaires.index', absolute: false), false)
        ->assertSee(route('academy.index', absolute: false), false)
        ->assertSee(route('forum.index', absolute: false), false)
        ->assertSee(__('hermes.nav.forum'))
        ->assertSee(route('profile.edit', absolute: false), false)
        ->assertSee(__('hermes.dashboard.logout'))
        ->assertSee('pill pill--neutral', false)
        ->assertDontSee('<button type="submit" class="pill pill--strong">'.__('hermes.dashboard.logout').'</button>', false)
        ->assertDontSee(__('hermes.nav.login'))
        ->assertDontSee(__('hermes.header.booking'));
});

test('published blog posts have a public detail page and drafts do not', function () {
    $publishedPost = BlogPost::factory()->create([
        'title' => [
            'nl' => 'Strategisch blogdetail',
            'en' => 'Strategic blog detail',
            'de' => 'Strategisches Blogdetail',
        ],
        'content' => [
            'nl' => "# Kop\n\nInhoud van de blogpost.",
            'en' => "# Heading\n\nBlog post content.",
            'de' => "# Uberschrift\n\nInhalt des Blogbeitrags.",
        ],
    ]);

    $draftPost = BlogPost::factory()->draft()->create();

    $this->get(route('blog.show', $publishedPost))
        ->assertOk()
        ->assertSee($publishedPost->titleForLocale())
        ->assertSee('user-page-heading', false)
        ->assertSee('user-section-heading', false)
        ->assertSee('user-action-row', false)
        ->assertSee('user-inline-meta', false)
        ->assertSee('user-surface-card', false)
        ->assertDontSee(__('hermes.blog.contact_action'))
        ->assertSee('Inhoud van de blogpost.');

    $this->get(route('blog.show', $draftPost))->assertNotFound();
});

test('public blog treats headings without a space after hashes as headings', function () {
    $blogPost = BlogPost::factory()->create([
        'content' => [
            'nl' => "#Kop zonder spatie\n\n##Tussenkop zonder spatie\n\nInhoud van de blogpost.",
            'en' => '',
            'de' => '',
        ],
    ]);

    $this->get(route('blog.show', $blogPost))
        ->assertOk()
        ->assertSee('<h1>Kop zonder spatie</h1>', false)
        ->assertSee('<h2>Tussenkop zonder spatie</h2>', false)
        ->assertSee('Inhoud van de blogpost.');
});

test('published blog posts without cover image do not show a placeholder block', function () {
    $blogPost = BlogPost::factory()->create([
        'cover_image_url' => null,
        'title' => [
            'nl' => 'Blog zonder omslag',
            'en' => 'Blog without cover',
            'de' => 'Blog ohne Cover',
        ],
    ]);

    $this->get(route('blog.show', $blogPost))
        ->assertOk()
        ->assertDontSee('article-cover--placeholder', false);
});

test('public blog detail includes seo metadata and tag navigation', function () {
    $blogPost = BlogPost::factory()->create([
        'tags' => ['AI adoptie', 'Leiderschap'],
        'title' => [
            'nl' => 'Seo blogdetail',
            'en' => 'Seo blog detail',
            'de' => 'Seo Blogdetail',
        ],
        'excerpt' => [
            'nl' => 'Beknopte samenvatting voor zoekmachines en social previews.',
            'en' => 'Short summary for search engines and social previews.',
            'de' => 'Kurze Zusammenfassung fur Suchmaschinen und Social Previews.',
        ],
    ]);

    $this->get(route('blog.show', $blogPost))
        ->assertOk()
        ->assertSee('<meta name="description" content="Beknopte samenvatting voor zoekmachines en social previews.">', false)
        ->assertSee('<meta property="og:title" content="Seo blogdetail">', false)
        ->assertSee('"@type": "Article"', false)
        ->assertSee(route('blog.index', ['tag' => 'AI adoptie']), false);
});

test('published blog posts render video shortcodes without requiring raw html', function () {
    $asset = MediaAsset::factory()->create([
        'asset_type' => MediaAsset::TYPE_VIDEO,
        'mime_type' => 'video/mp4',
        'extension' => 'mp4',
        'path' => 'media-assets/2026/04/intro-video.mp4',
    ]);

    $blogPost = BlogPost::factory()->create([
        'content' => [
            'nl' => "Intro tekst.\n\n".$asset->embedSnippet()."\n\nAfsluiting.",
            'en' => "Intro text.\n\n".$asset->embedSnippet()."\n\nClosing.",
            'de' => "Intro Text.\n\n".$asset->embedSnippet()."\n\nAbschluss.",
        ],
    ]);

    $this->get(route('blog.show', $blogPost))
        ->assertOk()
        ->assertSee('Intro tekst.')
        ->assertSee('<video controls', false)
        ->assertSee($asset->absoluteUrl(), false)
        ->assertSee('Afsluiting.');
});

test('published blog posts render image shortcodes with width and alignment', function () {
    $blogPost = BlogPost::factory()->create([
        'content' => [
            'nl' => "Intro tekst.\n\n[image url=\"http://localhost:8000/storage/media-assets/2026/04/demo.png\" alt=\"Screenshot\" width=\"320\" align=\"right\"]\n\nAfsluiting.",
            'en' => '',
            'de' => '',
        ],
    ]);

    $this->get(route('blog.show', $blogPost))
        ->assertOk()
        ->assertSee('Intro tekst.')
        ->assertSee('article-media article-media--right', false)
        ->assertSee('max-width: 320px', false)
        ->assertSee('<img src="http://localhost:8000/storage/media-assets/2026/04/demo.png" alt="Screenshot"', false)
        ->assertSee('Afsluiting.');
});

test('public blog falls back to dutch when a translation is empty or trivial', function () {
    $blogPost = BlogPost::factory()->create([
        'title' => [
            'nl' => 'Nederlandse blogtitel',
            'en' => '...',
            'de' => null,
        ],
        'excerpt' => [
            'nl' => 'Nederlandse samenvatting',
            'en' => '...',
            'de' => null,
        ],
        'content' => [
            'nl' => "# Nederlandse kop\n\nNederlandse inhoud met afbeelding.\n\n![Screenshot](http://localhost:8000/storage/media-assets/2026/04/demo.png)",
            'en' => '...',
            'de' => '',
        ],
    ]);

    $this->withSession(['locale' => 'en'])
        ->get(route('blog.show', $blogPost))
        ->assertOk()
        ->assertSee('Nederlandse blogtitel')
        ->assertSee('Nederlandse inhoud met afbeelding.')
        ->assertSee('<img src="http://localhost:8000/storage/media-assets/2026/04/demo.png" alt="Screenshot" />', false);
});

test('sitemap only contains the blog index and published blog posts', function () {
    $publishedPost = BlogPost::factory()->create([
        'slug' => 'publieke-sitemap-post',
    ]);

    BlogPost::factory()->draft()->create([
        'slug' => 'concept-sitemap-post',
    ]);

    $this->get(route('sitemap'))
        ->assertOk()
        ->assertHeader('content-type', 'application/xml; charset=UTF-8')
        ->assertSee(route('blog.index'), false)
        ->assertSee(route('blog.show', $publishedPost), false)
        ->assertDontSee('concept-sitemap-post');
});
