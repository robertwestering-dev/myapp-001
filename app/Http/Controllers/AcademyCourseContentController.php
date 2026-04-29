<?php

namespace App\Http\Controllers;

use App\Models\AcademyCourse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class AcademyCourseContentController extends Controller
{
    public function __invoke(Request $request, string $academyCoursePath, ?string $asset = null): BinaryFileResponse
    {
        $user = $request->user();
        $academyCourse = AcademyCourse::query()
            ->where('path', 'academy-courses/'.trim($academyCoursePath, '/'))
            ->where('is_active', true)
            ->firstOrFail();

        abort_unless($user !== null, 403);
        abort_unless($academyCourse->canBeLaunchedBy($user), 403);

        $relativeAssetPath = $this->normalizedAssetPath($asset);
        $absolutePath = $academyCourse->contentPath($relativeAssetPath);

        abort_unless(is_file($absolutePath), 404);

        return response()->file($absolutePath, [
            'Content-Type' => $this->contentTypeFor($absolutePath),
            'Content-Disposition' => (new ResponseHeaderBag)->makeDisposition(
                ResponseHeaderBag::DISPOSITION_INLINE,
                basename($absolutePath),
            ),
        ]);
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

    private function contentTypeFor(string $absolutePath): string
    {
        return match (strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION))) {
            'html', 'htm' => 'text/html; charset=UTF-8',
            'js', 'mjs' => 'application/javascript; charset=UTF-8',
            'css' => 'text/css; charset=UTF-8',
            'json', 'map' => 'application/json; charset=UTF-8',
            'svg' => 'image/svg+xml',
            'xml' => 'application/xml; charset=UTF-8',
            'txt' => 'text/plain; charset=UTF-8',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'otf' => 'font/otf',
            default => mime_content_type($absolutePath) ?: 'application/octet-stream',
        };
    }
}
