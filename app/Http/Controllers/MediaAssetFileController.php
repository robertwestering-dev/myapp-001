<?php

namespace App\Http\Controllers;

use App\Models\MediaAsset;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaAssetFileController extends Controller
{
    public function show(MediaAsset $mediaAsset): StreamedResponse
    {
        abort_unless($mediaAsset->existsOnDisk(), 404);

        return $mediaAsset->streamResponse();
    }

    public function legacy(string $mediaAssetPath): StreamedResponse
    {
        $normalizedPath = 'media-assets/'.ltrim($mediaAssetPath, '/');
        $mediaAsset = MediaAsset::query()->firstWhere('path', $normalizedPath);

        if ($mediaAsset instanceof MediaAsset) {
            abort_unless($mediaAsset->existsOnDisk(), 404);

            return $mediaAsset->streamResponse();
        }

        abort_unless(Storage::disk('public')->exists($normalizedPath), 404);

        return Storage::disk('public')->response($normalizedPath, basename($normalizedPath), [
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}
