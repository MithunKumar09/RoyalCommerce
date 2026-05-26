<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SyncImagesToS3 extends Command
{
    protected $signature = 'images:sync {--path=optimized-images : Directory under storage/app to sync} {--dry-run : Do not upload, only show files}';

    protected $description = 'Sync optimized images to S3 (or configured cloud disk). Use --dry-run to preview.';

    public function handle()
    {
        $path = $this->option('path') ?: 'optimized-images';
        $dry = $this->option('dry-run');

        $localDir = storage_path("app/{$path}");
        if (!is_dir($localDir)) {
            $this->warn("Local directory not found: {$localDir}");
            return 1;
        }

        $files = File::allFiles($localDir);
        $count = 0;

        if (!$dry) {
            if (empty(env('AWS_BUCKET')) || empty(env('AWS_ACCESS_KEY_ID')) || empty(env('AWS_SECRET_ACCESS_KEY'))) {
                $this->warn('AWS credentials or bucket not configured in .env. Aborting.');
                return 1;
            }

            $disk = Storage::disk('s3');
        }

        foreach ($files as $file) {
            $relative = str_replace($localDir . DIRECTORY_SEPARATOR, '', $file->getPathname());
            $s3Path = rtrim($path, '/') . '/' . str_replace('\\', '/', $relative);

            if ($dry) {
                $this->line("DRY: {$file->getFilename()} -> {$s3Path}");
                continue;
            }

            try {
                $stream = fopen($file->getPathname(), 'r');
                $disk->put($s3Path, $stream, 'public');
                if (is_resource($stream)) {
                    fclose($stream);
                }
                $this->line("Uploaded: {$s3Path}");
                $count++;
            } catch (\Exception $e) {
                $this->error("Failed: {$s3Path} - " . $e->getMessage());
            }
        }

        $this->info("Synced {$count} files to S3 (path: {$path})");
        $this->info('If using CloudFront, set CDN_URL to your distribution URL (e.g. https://dxxxxx.cloudfront.net)');

        return 0;
    }
}
