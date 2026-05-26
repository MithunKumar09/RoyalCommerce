<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImageOptimizationService;
use Illuminate\Support\Facades\File;

/**
 * PERFORMANCE FIX: Image Optimization Command
 * 
 * Optimizes all images in the application for web delivery
 * Generates WebP variants and responsive image sizes
 * 
 * Usage:
 *   php artisan images:optimize                    # Optimize all images
 *   php artisan images:optimize --path=thumbnails  # Optimize specific directory
 */
class OptimizeImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:optimize {--path= : Specific image directory to optimize (e.g., thumbnails, products)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize images: compress, convert to WebP, and generate responsive variants';

    /**
     * The image optimization service
     */
    protected ImageOptimizationService $imageService;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->imageService = new ImageOptimizationService();
        
        $specificPath = $this->option('path');
        
        if ($specificPath) {
            $this->optimizeDirectory($specificPath);
        } else {
            $this->optimizeAllImages();
        }
        
        $this->info('✅ Image optimization complete!');
    }

    /**
     * Optimize all images in the public assets directory
     */
    private function optimizeAllImages()
    {
        $this->info('🔍 Scanning assets/images directory...');
        
        $imageDirs = [
            'assets/images/thumbnails',
            'assets/images/products',
            'assets/images/categories',
            'assets/images/sliders',
            'assets/images/banners',
            'assets/images/arrival',
            'assets/images/galleries',
            'assets/images/blogs',
            'assets/images/reviews',
        ];
        
        foreach ($imageDirs as $dir) {
            $this->optimizeDirectory(str_replace('assets/images/', '', $dir));
        }
    }

    /**
     * Optimize all images in a specific directory
     */
    private function optimizeDirectory($dirPath)
    {
        // Handle both relative and full paths
        if (strpos($dirPath, 'assets/') === 0) {
            $fullPath = base_path($dirPath);
        } else {
            $fullPath = base_path("../assets/images/{$dirPath}");
        }
        
        if (!is_dir($fullPath)) {
            $this->warn("Directory not found: {$fullPath}");
            return;
        }
        
        $this->info("\n📁 Optimizing: {$dirPath}");
        
        $supportedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $files = File::files($fullPath);
        
        $count = 0;
        $skipped = 0;
        
        foreach ($files as $file) {
            $ext = strtolower($file->getExtension());
            
            // Skip if already optimized or not an image
            if (!in_array($ext, $supportedExtensions) || $file->getFilename() === 'noimage.png') {
                $skipped++;
                continue;
            }
            
            // Skip variant files (already optimized)
            if (preg_match('/-\d+w\./', $file->getFilename()) || preg_match('/\.webp$/', $file->getFilename())) {
                $skipped++;
                continue;
            }
            
            try {
                $this->line("  ⚙️  Processing: {$file->getFilename()}");
                
                $this->imageService->optimizeImage(
                    $file->getPathname(),
                    "optimized-images/{$dirPath}"
                );
                
                $count++;
                $this->getOutput()->write("    ✓ Optimized");
                
            } catch (\Exception $e) {
                $this->error("    ✗ Error: {$e->getMessage()}");
                $skipped++;
            }
        }
        
        $this->info("   Processed: {$count} | Skipped: {$skipped}");
    }
}
