<?php

namespace App\Helpers;

class MediaUrl
{
    /**
     * Normalize media URLs for production safety.
     * Converts absolute URLs (dev) to relative paths and ensures leading slash.
     * 
     * Examples:
     * - "http://127.0.0.1:8000/assets/products_media/19/360/frames/manifest.json"
     *   → "/assets/products_media/19/360/frames/manifest.json"
     * - "assets/products_media/19/hotspots/images/file.jpeg"
     *   → "/assets/products_media/19/hotspots/images/file.jpeg"
     * - "/assets/images/products/photo.jpg"
     *   → "/assets/images/products/photo.jpg"
     */
    public static function normalize(?string $path): ?string
    {
        if (!$path) return null;

        // Strip protocol + host if accidentally stored as absolute URL
        // Handles: http://127.0.0.1:8000/... or https://domain.com/...
        $path = preg_replace('#^https?://[^/]+#', '', $path);
        
        // Normalize slashes and ensure leading slash
        $path = trim($path);
        if (!$path) return null;

        // Ensure path starts with / so browser fetches from root, not relative to current page
        return '/' . ltrim($path, '/');
    }
}