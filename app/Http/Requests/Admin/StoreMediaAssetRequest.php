<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StoreMediaAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'asset' => [
                'required',
                File::types([
                    'jpg',
                    'jpeg',
                    'png',
                    'webp',
                    'gif',
                    'mp4',
                    'mov',
                    'webm',
                    'ogg',
                    'pdf',
                ])->max('50mb'),
            ],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ];
    }
}
