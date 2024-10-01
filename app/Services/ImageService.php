<?php

namespace App\Services;

use App\Models\{Product, ProductImage};
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    /**
     * Process and store multiple images for a given product.
     * Iterates through each image file, processes, and stores them in the specified location.
     *
     * @param Product $product the product to associate the images with
     * @param array   $images  an array of UploadedFile objects representing the images
     * @param string  $name    the name used for alternative text in images
     */
    public function processAndStoreImages(Product $product, array $images, string $name): void
    {
        foreach ($images as $imageFile) {
            $this->storeImage($product, $imageFile, $name);
        }
    }

    public function processAndStoreProfilePicture(UploadedFile $imageFile, string $path): string
    {
        // Save the original image temporarily
        $tempFilename = 'temp_' . time() . '.' . $imageFile->getClientOriginalExtension();
        $tempPath = "temp/{$tempFilename}";
        Storage::disk('public')->put($tempPath, file_get_contents($imageFile));

        $newFilename = 'profile_' . time() . '.webp';
        $newPath = "profile_pictures/{$newFilename}";

        // Process the image using the Node.js script
        $this->processImage($tempPath, $newPath, 'imageProcessorProfile.js');

        // Delete the temporary file
        Storage::disk('public')->delete($tempPath);

        // Delete the old image if it exists
        if (!empty($path) && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        return $newPath;
    }

    /**
     * Delete a product image from storage.
     * Removes the image file and its resized versions from the storage and deletes the corresponding ProductImage record.
     *
     * @param ProductImage $productImage the ProductImage instance to delete
     */
    public function deleteImage(ProductImage $productImage): void
    {
        Storage::delete($productImage->image_path);
        Storage::delete($productImage->resized_image_path);
        Storage::delete($productImage->show_image_path);
        Storage::delete($productImage->thumbnail_image_path);
        $productImage->delete();
    }

    /**
     * Store an individual image associated with a product.
     * Processes the image and creates various versions (original, resized, show, thumbnail).
     * Updates or creates a ProductImage record with the image paths.
     *
     * @param Product      $product   the product to associate the image with
     * @param UploadedFile $imageFile the image file to be processed and stored
     * @param string       $name      the name used for alternative text in images
     */
    private function storeImage(Product $product, UploadedFile $imageFile, string $name): void
    {
        $timestamp = time();
        $originalFilename = "product_{$timestamp}_original.webp";
        $originalImagePath = "product_images/{$originalFilename}";

        $resizedFilename = "product_{$timestamp}_resized.webp";
        $resizedImagePath = "product_images/{$resizedFilename}";

        $showFilename = "product_{$timestamp}_show.webp";
        $showImagePath = "product_images/{$showFilename}";

        $thumbnailFilename = "product_{$timestamp}_thumbnail.webp";
        $thumbnailImagePath = "product_images/{$thumbnailFilename}";

        // Save the original image
        $imageFile->storeAs('product_images', $originalFilename, 'public');

        // Process images (resizing, thumbnail creation, etc.)
        $this->processImage($originalImagePath, $resizedImagePath, 'imageProcessor.js');
        $this->processImage($originalImagePath, $showImagePath, 'imageProcessorShow.js');
        $this->processImage($originalImagePath, $thumbnailImagePath, 'imageProcessorThumbnail.js');

        // Create or update the ProductImage record
        ProductImage::updateOrCreate(
            [
                'product_id' => $product->id,
                'image_path' => $originalImagePath,
            ],
            [
                'resized_image_path' => $resizedImagePath,
                'show_image_path' => $showImagePath,
                'thumbnail_image_path' => $thumbnailImagePath,
                'alt_text' => $name,
            ],
        );
    }

    /**
     * Process an image using a specified processor script.
     * Applies a node.js script to perform operations like resizing on the image.
     *
     * @param string $sourcePath      path of the source image
     * @param string $destinationPath path to store the processed image
     * @param string $processorScript the script used to process the image
     */
    private function processImage(string $sourcePath, string $destinationPath, string $processorScript): void
    {
        $nodeCommand = "node " . escapeshellarg(base_path("resources/js/{$processorScript}")) . " " .
            escapeshellarg(storage_path("app/public/{$sourcePath}")) . " " .
            escapeshellarg(storage_path("app/public/{$destinationPath}"));

        exec($nodeCommand);
    }
}
