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

test('manager cannot delete media assets', function () {
    $manager = User::factory()->manager()->create();
    $mediaAsset = MediaAsset::factory()->create();

    $this->actingAs($manager)
        ->delete(route('admin.media-assets.destroy', $mediaAsset))
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

test('public media asset route serves an uploaded image', function () {
    Storage::fake('public');

    $mediaAsset = MediaAsset::factory()->create([
        'disk' => 'public',
        'path' => UploadedFile::fake()->image('workshop.jpg', 1200, 800)
            ->store('media-assets/2026/05', 'public'),
        'original_name' => 'workshop.jpg',
        'mime_type' => 'image/jpeg',
        'extension' => 'jpg',
        'asset_type' => MediaAsset::TYPE_IMAGE,
    ]);

    $this->get(route('media-assets.show', $mediaAsset))
        ->assertOk()
        ->assertHeader('content-type', 'image/jpeg');
});

test('public media asset route returns not found when file is missing', function () {
    $mediaAsset = MediaAsset::factory()->create([
        'disk' => 'public',
        'path' => 'media-assets/2026/05/missing.jpg',
        'original_name' => 'missing.jpg',
        'mime_type' => 'image/jpeg',
        'extension' => 'jpg',
        'asset_type' => MediaAsset::TYPE_IMAGE,
    ]);

    $this->get(route('media-assets.show', $mediaAsset))
        ->assertNotFound();
});

test('legacy storage media asset route still serves uploaded files', function () {
    Storage::fake('public');

    $path = UploadedFile::fake()->image('legacy-workshop.jpg', 1200, 800)
        ->store('media-assets/2026/05', 'public');

    MediaAsset::factory()->create([
        'disk' => 'public',
        'path' => $path,
        'original_name' => 'legacy-workshop.jpg',
        'mime_type' => 'image/jpeg',
        'extension' => 'jpg',
        'asset_type' => MediaAsset::TYPE_IMAGE,
    ]);

    $this->get('/storage/'.$path)
        ->assertOk()
        ->assertHeader('content-type', 'image/jpeg');
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

test('admin can delete an asset and its stored file', function () {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();
    $path = UploadedFile::fake()->image('team-session.jpg', 1600, 900)
        ->store('media-assets/2026/05', 'public');

    $mediaAsset = MediaAsset::factory()->create([
        'disk' => 'public',
        'path' => $path,
        'original_name' => 'team-session.jpg',
        'mime_type' => 'image/jpeg',
        'extension' => 'jpg',
        'asset_type' => MediaAsset::TYPE_IMAGE,
    ]);

    Storage::disk('public')->assertExists($path);

    $this->actingAs($admin)
        ->delete(route('admin.media-assets.destroy', $mediaAsset))
        ->assertRedirect(route('admin.media-assets.index'));

    expect(MediaAsset::query()->find($mediaAsset->id))->toBeNull();
    Storage::disk('public')->assertMissing($path);
});
