<?php

use App\Models\BlogPost;
use App\Models\User;

test('authenticated users can browse the blog in a forum-like overview without a post form', function () {
    $user = User::factory()->create();
    $blogPost = BlogPost::factory()->create([
        'tags' => ['AI adoptie', 'Leiderschap'],
        'title' => [
            'nl' => 'Forumachtige blogkaart',
            'en' => 'Forum style blog card',
            'de' => 'Blogkarte im Forenstil',
        ],
        'excerpt' => [
            'nl' => 'Een compact artikelblok in dezelfde visuele lijn als het forum.',
            'en' => 'A compact article card in the same visual style as the forum.',
            'de' => 'Eine kompakte Artikelkarte in derselben visuellen Linie wie das Forum.',
        ],
    ]);

    $this->actingAs($user)
        ->get(route('blog.index'))
        ->assertOk()
        ->assertSee(__('hermes.blog.summary_eyebrow'))
        ->assertSee(__('hermes.blog.summary_title'))
        ->assertSee(__('hermes.blog.editorial_note'))
        ->assertSee($blogPost->titleForLocale())
        ->assertSee('AI adoptie')
        ->assertSee(route('blog.show', $blogPost), false)
        ->assertDontSee(__('hermes.forum.compose.title'));
});
