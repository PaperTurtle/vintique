<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ProcessProductImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:process-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process product images to create resized and show versions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $originalImages = Storage::disk('public')->files('product_images');

        foreach ($originalImages as $imagePath) {
            if (str_ends_with($imagePath, '_original.webp')) {
                $this->info("Processing $imagePath");

                $resizedPath = str_replace('_original', '_resized', $imagePath);
                $showPath = str_replace('_original', '_show', $imagePath);

                $this->processImage($imagePath, $resizedPath);
                $this->processImage($imagePath, $showPath, true);
            }
        }

        $this->info('All images processed successfully.');
    }

    protected function processImage($sourcePath, $targetPath, $forShow = false)
    {
        $nodeScript = $forShow ? 'imageProcessorShow.js' : 'imageProcessor.js';
        $nodeCommand = "node " . escapeshellarg(base_path("resources/js/$nodeScript")) . " " .
            escapeshellarg(storage_path("app/public/$sourcePath")) . " " .
            escapeshellarg(storage_path("app/public/$targetPath"));

        exec($nodeCommand);
        $this->info("Processed $targetPath using $nodeScript");
    }
}