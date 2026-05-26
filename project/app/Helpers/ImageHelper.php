<?php

namespace App\Helpers;

use App\Services\ResponsiveImageService;

/**
 * PERFORMANCE FIX: Image Helper
 * 
 * Convenient functions for use in Blade templates
 * Usage: {{ image_responsive('assets/images/hero.jpg', 'Alt text') }}
 */

if (!function_exists('image_responsive')) {
    /**
     * Render responsive image with srcset
     * 
     * @param string $path Image path
     * @param string $alt Alt text
     * @param array $options Options (class, width, height, webp, loading)
     * @return string HTML
     */
    function image_responsive($path, $alt = '', $options = [])
    {
        return ResponsiveImageService::responsive($path, $alt, $options);
    }
}

if (!function_exists('image_lazy')) {
    /**
     * Render lazy-loaded image
     * 
     * @param string $path Image path
     * @param string $alt Alt text
     * @param array $options Options
     * @return string HTML
     */
    function image_lazy($path, $alt = '', $options = [])
    {
        return ResponsiveImageService::lazyWithPlaceholder($path, $alt, array_merge($options, [
            'loading' => 'lazy'
        ]));
    }
}

if (!function_exists('image_preload')) {
    /**
     * Generate preload link for critical image
     * Add to <head> section
     * 
     * @param string $path Image path
     * @return string HTML link tags
     */
    function image_preload($path)
    {
        return ResponsiveImageService::preload($path);
    }
}

if (!function_exists('image_eager')) {
    /**
     * Render eager-loaded image (for above-fold critical images)
     * 
     * @param string $path Image path
     * @param string $alt Alt text
     * @param array $options Options
     * @return string HTML
     */
    function image_eager($path, $alt = '', $options = [])
    {
        return ResponsiveImageService::responsive($path, $alt, array_merge($options, [
            'loading' => 'eager'
        ]));
    }
}
