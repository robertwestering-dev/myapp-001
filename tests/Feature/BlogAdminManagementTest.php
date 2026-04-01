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
        ->assertSee('Blogbeheer')
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

function translatedBlogPayload(string $value): array
{
    return collect(array_keys(config('locales.supported', [])))
        ->mapWithKeys(fn (string $locale): array => [$locale => $value.' '.$locale])
        ->all();
}
