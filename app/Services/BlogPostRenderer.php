<?php

namespace App\Services;

use Illuminate\Support\Str;

class BlogPostRenderer
{
    /**
     * Render raw Markdown blog content to HTML, processing media shortcodes.
     */
    public function render(string $content): string
    {
        $placeholders = [];
        $content = $this->replaceMediaShortcodes($content, $placeholders);
        $content = $this->normalizeMarkdownHeadings($content);

        $rendered = Str::markdown($content);

        foreach ($placeholders as $placeholder => $html) {
            $rendered = str_replace("<p>{$placeholder}</p>", $html, $rendered);
            $rendered = str_replace($placeholder, $html, $rendered);
        }

        return $rendered;
    }

    /**
     * @param  array<string, string>  $placeholders
     */
    protected function replaceMediaShortcodes(string $content, array &$placeholders): string
    {
        $content = preg_replace_callback(
            '/\[image\s+([^\]]+)\]/i',
            function (array $matches) use (&$placeholders): string {
                $attributes = $this->parseShortcodeAttributes($matches[1] ?? '');
                $url = $attributes['url'] ?? null;

                if (! is_string($url) || trim($url) === '') {
                    return $matches[0];
                }

                $placeholder = '%%BLOG_IMAGE_'.count($placeholders).'%%';
                $placeholders[$placeholder] = $this->buildImageHtml($attributes);

                return $placeholder;
            },
            $content,
        ) ?? $content;

        return preg_replace_callback(
            '/\[video(?:\s+([^\]]+)|(?:\s+url=|:|\s+)(?:"([^"]+)"|\'([^\']+)\'|([^\]\s]+)))\]/i',
            function (array $matches) use (&$placeholders): string {
                $attributes = isset($matches[1]) && is_string($matches[1]) && trim($matches[1]) !== ''
                    ? $this->parseShortcodeAttributes($matches[1])
                    : ['url' => $matches[2] ?: $matches[3] ?: $matches[4] ?: null];
                $url = $attributes['url'] ?? null;

                if (! is_string($url) || trim($url) === '') {
                    return $matches[0];
                }

                $placeholder = '%%BLOG_VIDEO_'.count($placeholders).'%%';
                $placeholders[$placeholder] = $this->buildVideoHtml($attributes);

                return $placeholder;
            },
            $content,
        ) ?? $content;
    }

    protected function normalizeMarkdownHeadings(string $content): string
    {
        return preg_replace('/^(#{1,3})(\S.*)$/m', '$1 $2', $content) ?? $content;
    }

    /**
     * @return array<string, string>
     */
    protected function parseShortcodeAttributes(string $source): array
    {
        preg_match_all('/(\w+)="([^"]*)"/', $source, $matches, PREG_SET_ORDER);

        return collect($matches)
            ->mapWithKeys(fn (array $match): array => [$match[1] => $match[2]])
            ->all();
    }

    /**
     * @param  array<string, string>  $attributes
     */
    protected function buildImageHtml(array $attributes): string
    {
        $url = trim((string) ($attributes['url'] ?? ''));
        $alt = trim((string) ($attributes['alt'] ?? ''));

        return sprintf(
            '<figure class="%s"%s><img src="%s" alt="%s"%s></figure>',
            e($this->mediaAlignmentClass($attributes['align'] ?? null)),
            $this->mediaWrapperStyle($attributes),
            e($url),
            e($alt),
            $this->mediaElementStyle($attributes)
        );
    }

    /**
     * @param  array<string, string>  $attributes
     */
    protected function buildVideoHtml(array $attributes): string
    {
        $url = trim((string) ($attributes['url'] ?? ''));

        return sprintf(
            '<figure class="%s"%s><video controls src="%s"%s></video></figure>',
            e($this->mediaAlignmentClass($attributes['align'] ?? null)),
            $this->mediaWrapperStyle($attributes),
            e($url),
            $this->mediaElementStyle($attributes)
        );
    }

    protected function mediaAlignmentClass(?string $align): string
    {
        return match ($align) {
            'left' => 'article-media article-media--left',
            'right' => 'article-media article-media--right',
            default => 'article-media article-media--center',
        };
    }

    /**
     * @param  array<string, string>  $attributes
     */
    protected function mediaWrapperStyle(array $attributes): string
    {
        $styles = [];

        if (($attributes['width'] ?? '') !== '') {
            $styles[] = 'max-width: '.$this->normalizeCssDimension($attributes['width']);
        }

        if ($styles === []) {
            return '';
        }

        return ' style="'.e(implode('; ', $styles)).'"';
    }

    /**
     * @param  array<string, string>  $attributes
     */
    protected function mediaElementStyle(array $attributes): string
    {
        $styles = ['width: 100%', 'height: auto'];

        if (($attributes['height'] ?? '') !== '') {
            $styles[1] = 'height: '.$this->normalizeCssDimension($attributes['height']);
            $styles[] = 'object-fit: cover';
        }

        return ' style="'.e(implode('; ', $styles)).'"';
    }

    protected function normalizeCssDimension(string $value): string
    {
        $trimmedValue = trim($value);

        if (preg_match('/^\d+$/', $trimmedValue) === 1) {
            return $trimmedValue.'px';
        }

        if (preg_match('/^\d+(\.\d+)?(px|em|rem|%|vw|vh|svh|dvh|ch|ex)$/', $trimmedValue) === 1) {
            return $trimmedValue;
        }

        return '100%';
    }
}
