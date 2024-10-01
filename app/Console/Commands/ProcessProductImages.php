<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * A Laravel console command to process product images and create resized and show versions.
 *
 * This command processes original product images stored in the 'public/product_images' directory.
 * It creates resized and show versions of the images based on predefined Node.js scripts.
 * The command is designed to maintain different versions of product images for various use cases.
 *
 * Usage: Run the command using the Laravel Artisan command line tool.
 * Command: `php artisan images:ppi` (process-product-images).
 */
class ProcessProductImages extends Command
{
    public const TYPE_DEFAULT = 'default';

    public const TYPE_SHOW = 'show';

    public const TYPE_THUMBNAIL = 'thumbnail';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:ppi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process product images to create resized and show versions';

    /** Execute the console command. */
    public function handle(): void
    {
        $originalImages = Storage::disk('public')->files('product_images');

        foreach ($originalImages as $imagePath) {
            if (str_ends_with($imagePath, '_original.webp')) {
                $this->info("Processing {$imagePath}");

                $this->processImageIfNotExists($imagePath, '_resized', self::TYPE_DEFAULT);
                $this->processImageIfNotExists($imagePath, '_show', self::TYPE_SHOW);
                $this->processImageIfNotExists($imagePath, '_thumbnail', self::TYPE_THUMBNAIL);
            }
        }

        $this->info('All images processed successfully.');
    }

    protected function processImageIfNotExists(string $imagePath, string $suffix, string $type): void
    {
        $newPath = str_replace('_original', $suffix, $imagePath);

        if (!Storage::disk('public')->exists($newPath)) {
            $this->processImage($imagePath, $newPath, $type);
        }
    }

    /**
     * Process an image using a Node.js script to create resized or show versions.
     *
     * @param string $sourcePath the source image file path
     * @param string $targetPath the target image file path to store the processed image
     * @param string $type       the type of processing to apply (default, show, or thumbnail)
     */
    protected function processImage(string $sourcePath, string $targetPath, string $type = self::TYPE_DEFAULT): void
    {
        $nodeScript = match ($type) {
            self::TYPE_SHOW => 'imageProcessorShow.js',
            self::TYPE_THUMBNAIL => 'imageProcessorThumbnail.js',
            default => 'imageProcessor.js',
        };

        $nodeCommand = "node " . escapeshellarg(base_path("resources/js/{$nodeScript}")) . " " .
            escapeshellarg(storage_path("app/public/{$sourcePath}")) . " " .
            escapeshellarg(storage_path("app/public/{$targetPath}"));

        $output = null;
        $returnVar = null;
        exec($nodeCommand, $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error("Failed to process {$targetPath} using {$nodeScript}");

            return;
        }

        $this->info("Processed {$targetPath} using {$nodeScript}");
    }
}
