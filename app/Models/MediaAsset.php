<?php

namespace App\Models;

use Database\Factories\MediaAssetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Fillable([
    'uploaded_by',
    'disk',
    'path',
    'original_name',
    'mime_type',
    'extension',
    'asset_type',
    'size_bytes',
    'alt_text',
])]
class MediaAsset extends Model
{
    public const TYPE_FILE = 'file';

    public const TYPE_IMAGE = 'image';

    public const TYPE_VIDEO = 'video';

    /** @use HasFactory<MediaAssetFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
        ];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function url(): string
    {
        return route('media-assets.show', ['mediaAsset' => $this], absolute: false);
    }

    public function absoluteUrl(): string
    {
        return route('media-assets.show', ['mediaAsset' => $this]);
    }

    public function existsOnDisk(): bool
    {
        return Storage::disk($this->disk)->exists($this->path);
    }

    public function deleteFile(): void
    {
        if (! $this->existsOnDisk()) {
            return;
        }

        Storage::disk($this->disk)->delete($this->path);
    }

    public function streamResponse(): StreamedResponse
    {
        return Storage::disk($this->disk)->response($this->path, $this->original_name, [
            'Cache-Control' => 'public, max-age=31536000',
            'Content-Type' => $this->mime_type,
        ]);
    }

    public function isImage(): bool
    {
        return $this->asset_type === self::TYPE_IMAGE;
    }

    public function isVideo(): bool
    {
        return $this->asset_type === self::TYPE_VIDEO;
    }

    public function embedSnippet(): string
    {
        $assetUrl = $this->absoluteUrl();
        $altText = $this->alt_text ?: $this->original_name;

        return match ($this->asset_type) {
            self::TYPE_IMAGE => sprintf('![%s](%s)', $altText, $assetUrl),
            self::TYPE_VIDEO => sprintf('[video url="%s"]', $assetUrl),
            default => sprintf('[%s](%s)', $this->original_name, $assetUrl),
        };
    }

    public function formattedSize(): string
    {
        return Number::fileSize($this->size_bytes);
    }
}
