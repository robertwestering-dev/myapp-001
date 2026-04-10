<?php

namespace App\Models;

use Database\Factories\MediaAssetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;

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
        return Storage::disk($this->disk)->url($this->path);
    }

    public function absoluteUrl(): string
    {
        return url($this->url());
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
