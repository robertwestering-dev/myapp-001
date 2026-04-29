<?php

namespace App\Http\Controllers;

use App\Models\AcademyCourse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AcademyCourseContentController extends Controller
{
    public function __invoke(Request $request, AcademyCourse $academyCourse, ?string $asset = null): BinaryFileResponse
    {
        $user = $request->user();

        abort_unless($user !== null, 403);
        abort_unless($academyCourse->is_active, 404);
        abort_unless($academyCourse->canBeLaunchedBy($user), 403);

        $relativeAssetPath = $this->normalizedAssetPath($asset);
        $absolutePath = $academyCourse->contentPath($relativeAssetPath);

        abort_unless(is_file($absolutePath), 404);

        return response()->file($absolutePath);
    }

    private function normalizedAssetPath(?string $asset): ?string
    {
        if ($asset === null || $asset === '') {
            return null;
        }

        $normalizedAssetPath = trim(str_replace('\\', '/', $asset), '/');

        abort_if(
            $normalizedAssetPath === ''
                || str_contains($normalizedAssetPath, '../')
                || str_starts_with($normalizedAssetPath, '..'),
            404,
        );

        return $normalizedAssetPath;
    }
}
