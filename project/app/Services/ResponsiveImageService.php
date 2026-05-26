<?php

namespace App\Services;

/**
 * PERFORMANCE FIX: Responsive Image Service
 * 
 * Generates HTML srcset and sizes attributes for responsive images
 * Serves optimized images based on device size
 */
class ResponsiveImageService
{
    /**
     * Generate responsive image HTML
     * 
     * @param string $imagePath Base image path (without extension)
     * @param string $alt Alt text
     * @param array $options Additional options
     * @return string HTML img tag with srcset and sizes
     */
    public static function responsive($imagePath, $alt = '', $options = [])
    {
        $loading = $options['loading'] ?? 'lazy';
        $class = $options['class'] ?? '';
        $width = $options['width'] ?? null;
        $height = $options['height'] ?? null;
        $useWebp = $options['webp'] ?? true;

        // Get file info
        $ext = pathinfo($imagePath, PATHINFO_EXTENSION);
        $basePath = pathinfo($imagePath, PATHINFO_DIRNAME) . '/' . pathinfo($imagePath, PATHINFO_FILENAME);

        // Build srcset variations
        $srcset = [];
        $srcsetWebp = [];

        // Original and variants
        $variants = [480, 768, 1920];

        foreach ($variants as $width) {
            $path = "{$basePath}-{$width}w.{$ext}";
                if (file_exists(public_path($path))) {
                    $srcset[] = cdn_asset($path) . " {$width}w";
                }

            if ($useWebp) {
                $webpPath = "{$basePath}-{$width}w.webp";
                if (file_exists(public_path($webpPath))) {
                    $srcsetWebp[] = cdn_asset($webpPath) . " {$width}w";
                }
            }
        }

        // Add original if variants don't exist
        if (empty($srcset)) {
            $srcset[] = cdn_asset($imagePath) . " 1920w";
        }

        // Build sizes attribute
        $sizes = "(max-width: 480px) 100vw, (max-width: 768px) 100vw, 100vw";

        // Build HTML
        $classAttr = $class ? " class=\"{$class}\"" : '';
        $widthAttr = $width ? " width=\"{$width}\"" : '';
        $heightAttr = $height ? " height=\"{$height}\"" : '';
        $srcsetAttr = implode(', ', $srcset);
        $srcsetWebpAttr = !empty($srcsetWebp) ? implode(', ', $srcsetWebp) : '';

        $html = '';

        // Add picture element for WebP with fallback
        if ($useWebp && !empty($srcsetWebpAttr)) {
            $html .= '<picture>';
            $html .= "\n  <source srcset=\"{$srcsetWebpAttr}\" sizes=\"{$sizes}\" type=\"image/webp\">";
            $html .= "\n  ";
        }

        $html .= "<img src=\"" . cdn_asset($imagePath) . "\" srcset=\"{$srcsetAttr}\" sizes=\"{$sizes}\" alt=\"{$alt}\" loading=\"{$loading}\"{$classAttr}{$widthAttr}{$heightAttr}>";

        if ($useWebp && !empty($srcsetWebpAttr)) {
            $html .= "\n</picture>";
        }

        return $html;
    }

    /**
     * Generate lazy loading image placeholder (LQIP - Low Quality Image Placeholder)
     * 
     * @param string $imagePath
     * @param string $alt
     * @param array $options
     * @return string HTML with blur-up effect
     */
    public static function lazyWithPlaceholder($imagePath, $alt = '', $options = [])
    {
        $class = $options['class'] ?? '';
        $width = $options['width'] ?? null;
        $height = $options['height'] ?? null;

        // Create placeholder data URL (tiny blurred image)
        $placeholder = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"%3E%3Crect fill="%23f0f0f0" width="400" height="300"/%3E%3C/svg%3E';

        $classAttr = $class ? " class=\"{$class}\"" : '';
        $widthAttr = $width ? " width=\"{$width}\"" : '';
        $heightAttr = $height ? " height=\"{$height}\"" : '';

        return self::responsive($imagePath, $alt, array_merge($options, [
            'loading' => 'lazy',
            'class' => "{$class} lazy-image",
        ]));
    }

    /**
     * Preload critical images (hero, above fold)
     * Add to <head> of page
     * 
     * @param string $imagePath
     * @return string HTML link tags for preloading
     */
    public static function preload($imagePath)
    {
        $ext = pathinfo($imagePath, PATHINFO_EXTENSION);
        $basePath = pathinfo($imagePath, PATHINFO_DIRNAME) . '/' . pathinfo($imagePath, PATHINFO_FILENAME);

        $html = '';

        // Preload WebP version first (preferred format)
        $webpPath = "{$basePath}.webp";
        if (file_exists(public_path($webpPath))) {
            $html .= '<link rel="preload" as="image" href="' . cdn_asset($webpPath) . '" type="image/webp">' . "\n";
        }

        // Fallback to original
        $html .= '<link rel="preload" as="image" href="' . cdn_asset($imagePath) . '" type="image/' . $ext . '">' . "\n";

        return $html;
    }
}
