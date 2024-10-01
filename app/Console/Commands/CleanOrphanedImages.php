<?php

namespace App\Console\Commands;

use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

/**
 * A Laravel console command to clean orphaned images.
 *
 * This command interfaces with the ProductController to remove images
 * from the storage that are no longer linked to any records in the database.
 * It helps in maintaining the storage and ensuring that only relevant
 * images are retained.
 *
 * Usage: Run the command using the Laravel Artisan command line tool.
 * Command: `php artisan images:coi` (clean-orphaned-images).
 */
class CleanOrphanedImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:coi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes images from the storage that are not in the database';

    /** Execute the console command. */
    public function handle(): void
    {
        // Get all image paths from storage
        $allImagePaths = Storage::disk('public')->files('product_images');

        // Get all image paths from the database (both original and resized)
        $dbImagePaths = ProductImage::select(['image_path', 'resized_image_path', 'show_image_path', 'thumbnail_image_path'])->get()->toArray();

        $allDbImagePaths = array_unique(Arr::flatten($dbImagePaths));

        // Calculate the orphaned images by diffing storage and database paths
        $orphanedImages = array_diff($allImagePaths, $allDbImagePaths);

        // Delete all orphaned images from storage at once
        Storage::disk('public')->delete($orphanedImages);

        $orphanedImagesCount = count($orphanedImages);
        $this->info("Orphaned images cleaned successfully. Total: {$orphanedImagesCount}");
    }
}
