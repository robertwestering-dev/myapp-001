<?php

namespace Database\Factories;

use App\Models\MediaAsset;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<MediaAsset>
 */
class MediaAssetFactory extends Factory
{
    public function definition(): array
    {
        $fileName = Str::slug($this->faker->words(3, true)).'.jpg';

        return [
            'uploaded_by' => User::factory()->admin(),
            'disk' => 'public',
            'path' => 'media-assets/'.now()->format('Y/m').'/'.$fileName,
            'original_name' => $fileName,
            'mime_type' => 'image/jpeg',
            'extension' => 'jpg',
            'asset_type' => MediaAsset::TYPE_IMAGE,
            'size_bytes' => 245_000,
            'alt_text' => $this->faker->sentence(3),
        ];
    }
}
