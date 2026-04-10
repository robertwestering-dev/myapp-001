<?php

use App\Models\MediaAsset;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('manager cannot access media asset management', function () {
    $manager = User::factory()->manager()->create();

    $this->actingAs($manager)
        ->get(route('admin.media-assets.index'))
        ->assertForbidden();
});

test('admin can upload an image asset and see its embed snippet', function () {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.media-assets.store'), [
        'asset' => UploadedFile::fake()->image('team-session.jpg', 1600, 900),
        'alt_text' => 'Teamsessie voorjaar 2026',
    ]);

    $response->assertRedirect(route('admin.media-assets.index'));

    $mediaAsset = MediaAsset::query()->firstOrFail();

    Storage::disk('public')->assertExists($mediaAsset->path);

    expect($mediaAsset->uploaded_by)->toBe($admin->id);
    expect($mediaAsset->asset_type)->toBe(MediaAsset::TYPE_IMAGE);
    expect($mediaAsset->alt_text)->toBe('Teamsessie voorjaar 2026');

    $this->actingAs($admin)
        ->get(route('admin.media-assets.index'))
        ->assertOk()
        ->assertSee('Assetbibliotheek')
        ->assertSee($mediaAsset->original_name)
        ->assertSee($mediaAsset->absoluteUrl(), false)
        ->assertSee($mediaAsset->embedSnippet(), false);
});

test('admin can upload a video asset', function () {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.media-assets.store'), [
        'asset' => UploadedFile::fake()->create('intro-video.mp4', 12_000, 'video/mp4'),
        'alt_text' => 'Introvideo',
    ]);

    $response->assertRedirect(route('admin.media-assets.index'));

    $mediaAsset = MediaAsset::query()->firstOrFail();

    expect($mediaAsset->asset_type)->toBe(MediaAsset::TYPE_VIDEO);
    expect($mediaAsset->embedSnippet())->toContain('[video url=');
});
