<?php

use App\Models\BlogPost;

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
        ->assertSee(__('hermes.blog.heading'))
        ->assertSee($publishedPost->titleForLocale())
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
        ->assertSee('Inhoud van de blogpost.');

    $this->get(route('blog.show', $draftPost))->assertNotFound();
});
