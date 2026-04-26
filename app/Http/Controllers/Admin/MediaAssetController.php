<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMediaAssetRequest;
use App\Models\MediaAsset;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;

class MediaAssetController extends Controller
{
    public function index(): View
    {
        return view('admin.media-assets.index', [
            'mediaAssets' => MediaAsset::query()
                ->with('uploader')
                ->latest()
                ->paginate(config('app.per_page')),
        ]);
    }

    public function store(StoreMediaAssetRequest $request): RedirectResponse
    {
        $file = $request->file('asset');

        abort_unless($file instanceof UploadedFile, 422);

        /** @var User $actor */
        $actor = $request->user();

        MediaAsset::query()->create([
            'uploaded_by' => $actor->id,
            'disk' => 'public',
            'path' => $file->store('media-assets/'.now()->format('Y/m'), 'public'),
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType() ?: $file->getClientMimeType() ?: 'application/octet-stream',
            'extension' => $file->getClientOriginalExtension() ?: null,
            'asset_type' => $this->detectAssetType($file),
            'size_bytes' => $file->getSize() ?: 0,
            'alt_text' => $request->validated('alt_text') ?: null,
        ]);

        return redirect()
            ->route('admin.media-assets.index')
            ->with('status', __('hermes.admin.media_assets.uploaded'));
    }

    protected function detectAssetType(UploadedFile $file): string
    {
        $mimeType = $file->getMimeType() ?: $file->getClientMimeType() ?: '';
        $extension = strtolower($file->getClientOriginalExtension());

        if (str_starts_with($mimeType, 'image/')) {
            return MediaAsset::TYPE_IMAGE;
        }

        if (str_starts_with($mimeType, 'video/')) {
            return MediaAsset::TYPE_VIDEO;
        }

        if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            return MediaAsset::TYPE_IMAGE;
        }

        if (in_array($extension, ['mp4', 'mov', 'webm', 'ogg'], true)) {
            return MediaAsset::TYPE_VIDEO;
        }

        return MediaAsset::TYPE_FILE;
    }
}
