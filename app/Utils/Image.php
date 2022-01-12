<?php

namespace App\Utils;

use App\Item;
use App\Column;
use App\ItemRevision;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Image
{
    /**
     * Check if file exists in storage.
     *
     * @param  string $path
     * @return bool
     */
    public static function checkFileExists($path)
    {
        if (Storage::disk('public')->missing($path)) {
            Log::warning(__('items.file_not_found', ['file' => $path]));
            return false;
        }
        return true;
    }

    /**
     * Store size (bytes) of a given image file.
     *
     * @param  \App\Item $item
     * @param  string $image_path
     * @param  string $filename
     * @param  integer $fcolumn_id
     * @return void
     */
    public static function storeImageSize(Item $item, $image_path, $filename, $column_id)
    {
        if (Image::checkFileExists($image_path . $filename)) {
        
            // Get size (bytes) of image
            $size = Storage::disk('public')->size($image_path . $filename);
            Log::debug(__('items.image_size'), ['size' => $size]);
            
            // Get the colmap holding the config for columns containing image dimensions
            $cm = Column::find($column_id)->column_mapping()->first();
            
            // Find the column holding the image height
            $size_column = $cm->getConfigValue('image_size_col');
            if ($size_column) {
                $item->details()->updateOrCreate(
                    ['column_fk' => $size_column],
                    ['value_int' => $size]
                );
            } else {
                Log::warning(__('items.no_column_for_image_size'), ['colmap' => $cm->colmap_id]);
            }
        }
    }

    /**
     * Store width and height of a given image file.
     *
     * @param  \App\Item $item
     * @param  string $image_path
     * @param  string $filename
     * @param  integer $fcolumn_id
     * @return void
     */
    public static function storeImageDimensions(Item $item, $image_path, $filename, $column_id)
    {
        if (Image::checkFileExists($image_path . $filename)) {
        
            // Get original dimensions of image
            list($width_orig, $height_orig) = getimagesize(
                Storage::disk('public')->path($image_path . $filename)
            );
            Log::debug(__('items.image_dimensions'), ['width' => $width_orig, 'height' => $height_orig]);
            
            // Get the colmap holding the config for columns containing image dimensions
            $cm = Column::find($column_id)->column_mapping()->first();
            
            // Find the column holding the image width
            $width_column = $cm->getConfigValue('image_width_col');
            if ($width_column) {
                $item->details()->updateOrCreate(
                    ['column_fk' => $width_column],
                    ['value_int' => $width_orig]
                );
            } else {
                Log::warning(__('items.no_column_for_image_width'), ['colmap' => $cm->colmap_id]);
            }
            
            // Find the column holding the image height
            $height_column = $cm->getConfigValue('image_height_col');
            if ($height_column) {
                $item->details()->updateOrCreate(
                    ['column_fk' => $height_column],
                    ['value_int' => $height_orig]
                );
            } else {
                Log::warning(__('items.no_column_for_image_height'), ['colmap' => $cm->colmap_id]);
            }
        }
    }

    /**
     * Handle resizing of a given image file.
     *
     * @param  string $image_path
     * @param  string $filename
     * @return void
     */
    public static function processImageResizing($image_path, $filename)
    {
        if (Image::checkFileExists($image_path . $filename)) {
            
            // Get original dimensions of image
            list($width_orig, $height_orig) = getimagesize(
                Storage::disk('public')->path($image_path . $filename)
            );
            
            // Create small sized preview images if set in config
            if (config('media.preview_dir')) {
                Image::createResizedImage(
                    $image_path,
                    $filename,
                    $width_orig,
                    $height_orig,
                    config('media.preview_dir'),
                    config('media.preview_width'),
                    config('media.preview_height')
                );
            }
            // Create medium sized preview images if set in config
            if (config('media.medium_dir')) {
                Image::createResizedImage(
                    $image_path,
                    $filename,
                    $width_orig,
                    $height_orig,
                    config('media.medium_dir'),
                    config('media.medium_width'),
                    config('media.medium_height')
                );
            }
        }
    }

    /**
     * Create a resized version of a given image file.
     *
     * @param  string $image_path
     * @param  string $filename
     * @param  string $dest_path
     * @param  integer $width_thumb
     * @param  integer $height_thumb
     * @return void
     */
    private static function createResizedImage($src_path, $filename, $width_orig, $height_orig, $dest_path, $width_thumb, $height_thumb)
    {
        if (Image::checkFileExists($src_path . $filename)) {
            
            // Calculate new dimensions
            $ratio = min($width_thumb/$width_orig, $height_thumb/$height_orig);
            $width_thumb = $width_orig * $ratio;
            $height_thumb = $height_orig * $ratio;
            
            // Load original image and scale it to new size
            $original = imagecreatefromjpeg(Storage::disk('public')->path($src_path . $filename));
            $scaled = imagescale($original, $width_thumb, $height_thumb, IMG_SINC);
            
            // Store thumbnail to disk
            imagejpeg($scaled, Storage::disk('public')->path($dest_path) . $filename, 85);
            
            Log::info(__('items.resized_image_created') . $dest_path . $filename);
        }
    }
}
