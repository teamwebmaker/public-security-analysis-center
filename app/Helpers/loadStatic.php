<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

if (!function_exists('load_style')) {
    function load_style($path)
    {
        $cleanPath = '/' . ltrim($path, '/');

        try {
            $ts = '?v=' . File::lastModified(public_path() . $cleanPath);
        } catch (Exception $e) {
            $ts = '';
        }

        return '<link rel="stylesheet" href="' . asset($cleanPath) . $ts . '">';
    }
}

if (!function_exists('load_script')) {
    function load_script($path)
    {
        $cleanPath = '/' . ltrim($path, '/');

        try {
            $ts = '?v=' . File::lastModified(public_path() . $cleanPath);
        } catch (Exception $e) {
            $ts = '';
        }

        return '<script type="module" src="' . asset($cleanPath) . $ts . '"></script>';
    }
}

if (!function_exists('render_editor_content')) {
    function render_editor_content($value): string
    {
        $formatted = e((string) ($value ?? ''));

        $formatted = preg_replace('/\*\*([^*]+?)\*\*/s', '<strong class="editor-content-bold">$1</strong>', $formatted);
        $formatted = preg_replace('/(^|[^*])\*([^*\n]+?)\*(?!\*)/s', '$1<em class="editor-content-italic">$2</em>', $formatted);

        return nl2br($formatted, false);
    }
}

if (!function_exists('normalize_public_upload_path')) {
    function normalize_public_upload_path(?string $path, ?string $legacyDirectory = null): ?string
    {
        if (!$path) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return is_safe_public_upload_url($path) ? $path : null;
        }

        $path = ltrim(preg_replace('#/+#', '/', str_replace('\\', '/', $path)), '/');

        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        if (str_starts_with($path, 'public/')) {
            $path = substr($path, strlen('public/'));
        }

        if (!is_safe_public_upload_path($path)) {
            return null;
        }

        if (!str_contains($path, '/') && $legacyDirectory) {
            $legacyDirectory = trim(preg_replace('#/+#', '/', str_replace('\\', '/', $legacyDirectory)), '/');

            if (!is_safe_public_upload_path($legacyDirectory)) {
                return null;
            }

            $path = $legacyDirectory . '/' . $path;
        }

        if (!is_safe_public_upload_path($path)) {
            return null;
        }

        return $path;
    }
}

if (!function_exists('is_safe_public_upload_path')) {
    function is_safe_public_upload_path(string $path): bool
    {
        if ($path === '' || preg_match('/[\x00-\x1F\x7F]/', $path)) {
            return false;
        }

        foreach (explode('/', $path) as $segment) {
            if ($segment === '' || $segment === '.' || $segment === '..') {
                return false;
            }
        }

        return true;
    }
}

if (!function_exists('is_safe_public_upload_url')) {
    function is_safe_public_upload_url(string $url): bool
    {
        return in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true);
    }
}

if (!function_exists('uploaded_file_url')) {
    function uploaded_file_url(?string $path, ?string $legacyDirectory = null): ?string
    {
        $normalizedPath = normalize_public_upload_path($path, $legacyDirectory);

        if (!$normalizedPath) {
            return null;
        }

        if (filter_var($normalizedPath, FILTER_VALIDATE_URL) && is_safe_public_upload_url($normalizedPath)) {
            return $normalizedPath;
        }

        return Storage::disk('public')->url($normalizedPath);
    }
}
