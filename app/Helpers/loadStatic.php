<?php

use Illuminate\Support\Facades\File;

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
