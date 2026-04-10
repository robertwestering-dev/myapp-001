<?php

use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\User;

test('guests are redirected to login when visiting the forum', function () {
    $this->get(route('forum.index'))
        ->assertRedirect(route('login'));
});

test('authenticated users can browse the forum overview and see the shared navigation', function () {
    $user = User::factory()->create([
        'first_name' => 'Robert',
    ]);
    $thread = ForumThread::factory()->question()->create([
        'user_id' => $user->getKey(),
        'title' => 'Hoe pakken jullie AI adoptie aan?',
        'tags' => ['AI adoptie', 'Leiderschap'],
    ]);

    ForumReply::factory()->create([
        'forum_thread_id' => $thread->getKey(),
    ]);

    $this->actingAs($user)
        ->get(route('forum.index'))
        ->assertOk()
        ->assertSee(__('hermes.forum.compose.title'))
        ->assertSee(__('hermes.forum.filters.title'))
        ->assertSee(__('hermes.forum.tags.title'))
        ->assertSee(__('hermes.nav.questionnaires'))
        ->assertSee(__('hermes.nav.academy'))
        ->assertSee(__('hermes.nav.forum'))
        ->assertSee(__('hermes.nav.blog'))
        ->assertSee(__('hermes.nav.profile'))
        ->assertSee('Hoe pakken jullie AI adoptie aan?')
        ->assertSee('AI adoptie')
        ->assertSee(route('forum.show', $thread), false);
});

test('authenticated users can create a new forum thread', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('forum.store'), [
            '_source' => 'thread',
            'discussion_type' => ForumThread::TYPE_EXPERIENCE,
            'title' => 'Onze eerste lessen uit een AI-pilot',
            'body' => "We zijn gestart met een kleine pilot.\n\nDe grootste winst zat in duidelijke begeleiding voor teams.",
            'tags' => 'AI adoptie, Pilot, Begeleiding',
        ])
        ->assertRedirect();

    $thread = ForumThread::query()->firstOrFail();

    expect($thread->user_id)->toBe($user->getKey())
        ->and($thread->discussion_type)->toBe(ForumThread::TYPE_EXPERIENCE)
        ->and($thread->tagList()->all())->toBe(['AI adoptie', 'Pilot', 'Begeleiding']);
});

test('authenticated users can reply to a forum thread', function () {
    $thread = ForumThread::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('forum-replies.store', $thread), [
            'body' => "Wij zagen hetzelfde patroon.\n\nEen korte onboarding voor leidinggevenden maakte veel verschil.",
        ])
        ->assertRedirect(route('forum.show', $thread).'#reply-form');

    $reply = ForumReply::query()->firstOrFail();

    expect($reply->forum_thread_id)->toBe($thread->getKey())
        ->and($reply->user_id)->toBe($user->getKey());
});

test('forum filters can narrow discussions by type and tag', function () {
    $user = User::factory()->create();
    ForumThread::factory()->create([
        'discussion_type' => ForumThread::TYPE_QUESTION,
        'title' => 'Vraag over digitale weerbaarheid',
        'tags' => ['Digitale weerbaarheid'],
    ]);
    ForumThread::factory()->create([
        'discussion_type' => ForumThread::TYPE_EXPERIENCE,
        'title' => 'Ervaring met teamtraining',
        'tags' => ['Leiderschap'],
    ]);

    $this->actingAs($user)
        ->get(route('forum.index', [
            'type' => ForumThread::TYPE_QUESTION,
            'tag' => 'Digitale weerbaarheid',
        ]))
        ->assertOk()
        ->assertSee('Vraag over digitale weerbaarheid')
        ->assertDontSee('Ervaring met teamtraining');
});

test('forum detail page shows replies and the reply form for authenticated users', function () {
    $thread = ForumThread::factory()->create([
        'title' => 'Samen leren van verandertrajecten',
    ]);
    $reply = ForumReply::factory()->create([
        'forum_thread_id' => $thread->getKey(),
        'body' => "Wij plannen reflectiemomenten na elke sprint.\n\nDat helpt om frustratie vroeg te signaleren.",
    ]);
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('forum.show', $thread))
        ->assertOk()
        ->assertSee('Samen leren van verandertrajecten')
        ->assertSee($reply->author->first_name ?: $reply->author->name)
        ->assertSee(__('hermes.forum.reply_form.title'))
        ->assertSee(route('forum-replies.store', $thread), false);
});

test('users can update their own forum replies', function () {
    $user = User::factory()->create();
    $thread = ForumThread::factory()->create();
    $reply = ForumReply::factory()->create([
        'forum_thread_id' => $thread->getKey(),
        'user_id' => $user->getKey(),
        'body' => 'Oude reactie tekst.',
    ]);

    $this->actingAs($user)
        ->put(route('forum-replies.update', [$thread, $reply]), [
            'body' => "Bijgewerkte reactie.\n\nMet extra context.",
        ])
        ->assertRedirect(route('forum.show', $thread).'#reply-'.$reply->getKey());

    expect($reply->fresh()->body)->toBe("Bijgewerkte reactie.\n\nMet extra context.");
});

test('users can delete their own forum replies', function () {
    $user = User::factory()->create();
    $thread = ForumThread::factory()->create();
    $reply = ForumReply::factory()->create([
        'forum_thread_id' => $thread->getKey(),
        'user_id' => $user->getKey(),
    ]);

    $this->actingAs($user)
        ->delete(route('forum-replies.destroy', [$thread, $reply]))
        ->assertRedirect(route('forum.show', $thread).'#replies');

    $this->assertModelMissing($reply);
});

test('users cannot edit replies from other users', function () {
    $user = User::factory()->create();
    $reply = ForumReply::factory()->create();
    $thread = $reply->thread;

    $this->actingAs($user)
        ->put(route('forum-replies.update', [$thread, $reply]), [
            'body' => 'Ongeoorloofde wijziging.',
        ])
        ->assertForbidden();
});
