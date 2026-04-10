<?php

use App\Models\BlogPost;
use App\Models\User;

test('manager cannot access blog post management', function () {
    $manager = User::factory()->manager()->create();

    $this->actingAs($manager)
        ->get(route('admin.blog-posts.index'))
        ->assertForbidden();
});

test('admin can open blog management overview', function () {
    $admin = User::factory()->admin()->create();
    $blogPost = BlogPost::factory()->create([
        'title' => [
            'nl' => 'Admin blog overzicht',
            'en' => 'Admin blog overview',
            'de' => 'Admin-Blogubersicht',
        ],
    ]);

    $this->actingAs($admin)
        ->get(route('admin.blog-posts.index'))
        ->assertOk()
        ->assertDontSee('Blogbeheer')
        ->assertSee('admin-status-badge', false)
        ->assertSee($blogPost->titleForLocale('nl'));
});

test('admin can create a blog post', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.blog-posts.store'), [
        'slug' => 'betrouwbare-digitale-verandering',
        'cover_image_url' => 'https://images.example.com/blog.jpg',
        'tags' => "Digitale transformatie\nLeiderschap",
        'published_at' => '2026-04-01 12:00:00',
        'is_published' => '1',
        'is_featured' => '1',
        'title' => translatedBlogPayload('Betrouwbare digitale verandering'),
        'excerpt' => translatedBlogPayload('Korte samenvatting voor de nieuwe blogpost'),
        'content' => translatedBlogPayload("# Titel\n\nInhoud van de blogpost"),
    ]);

    $response->assertRedirect();

    expect(BlogPost::query()->where('slug', 'betrouwbare-digitale-verandering')->exists())->toBeTrue();
});

test('admin can create a blog post with only dutch content filled in', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.blog-posts.store'), [
        'slug' => 'alleen-nederlandse-blogpost',
        'cover_image_url' => 'https://images.example.com/blog.jpg',
        'tags' => "Digitale transformatie\nLeiderschap",
        'published_at' => '2026-04-01 12:00:00',
        'is_published' => '1',
        'title' => [
            'nl' => 'Alleen Nederlandse titel',
            'en' => '',
            'de' => '',
        ],
        'excerpt' => [
            'nl' => 'Alleen Nederlandse samenvatting',
            'en' => '',
            'de' => '',
        ],
        'content' => [
            'nl' => "# Nederlandse titel\n\nNederlandse inhoud.",
            'en' => '',
            'de' => '',
        ],
    ]);

    $response->assertRedirect();

    $blogPost = BlogPost::query()->where('slug', 'alleen-nederlandse-blogpost')->firstOrFail();

    expect($blogPost->translation('title', 'nl'))->toBe('Alleen Nederlandse titel');
    expect($blogPost->translation('title', 'en'))->toBeNull();
    expect($blogPost->translation('title', 'de'))->toBeNull();
});

test('admin blog overview shows scheduled status and preview action', function () {
    $admin = User::factory()->admin()->create();
    $scheduledPost = BlogPost::factory()->create([
        'is_published' => true,
        'published_at' => now()->addDay(),
        'title' => [
            'nl' => 'Geplande blogpost',
            'en' => 'Scheduled blog post',
            'de' => 'Geplanter Blogbeitrag',
        ],
    ]);

    $this->actingAs($admin)
        ->get(route('admin.blog-posts.index'))
        ->assertOk()
        ->assertSee('Geplande blogpost')
        ->assertSee('admin-status-badge', false)
        ->assertSee('Gepland')
        ->assertSee(route('admin.blog-posts.preview', $scheduledPost), false);
});

test('admin can preview a draft blog post', function () {
    $admin = User::factory()->admin()->create();
    $blogPost = BlogPost::factory()->draft()->create([
        'title' => [
            'nl' => 'Preview blogpost',
            'en' => 'Preview blog post',
            'de' => 'Vorschau Blogbeitrag',
        ],
    ]);

    $this->actingAs($admin)
        ->get(route('admin.blog-posts.preview', $blogPost))
        ->assertOk()
        ->assertSee('Preview blogpost')
        ->assertSee('Previewmodus: deze weergave is alleen zichtbaar voor admins.');
});

test('admin blog form includes robust markdown preview support', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.blog-posts.create'))
        ->assertOk()
        ->assertSee('overflow-wrap: anywhere;', false)
        ->assertSee('trimmed.match(/^(#{1,3})\\s*(.+)$/);', false)
        ->assertSee("rendered.replace(/\\*\\*(.+?)\\*\\*/g, '<strong>\$1</strong>');", false)
        ->assertSee('const imageShortcodeMatch = trimmed.match(/^\\[image\\s+([^\\]]+)\\]$/i);', false);
});

function translatedBlogPayload(string $value): array
{
    return collect(array_keys(config('locales.supported', [])))
        ->mapWithKeys(fn (string $locale): array => [$locale => $value.' '.$locale])
        ->all();
}
