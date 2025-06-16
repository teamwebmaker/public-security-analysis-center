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
