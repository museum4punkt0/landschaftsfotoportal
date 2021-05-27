<?php

namespace App\Utils;

use App\Detail;
use App\Column;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Image
{
    /**
     * Store width and height of a given image file.
     *
     * @param  string $image_path
     * @param  string $filename
     * @param  integer $item_id
     * @param  integer $fcolumn_id
     * @return void
     */
    public static function storeImageDimensions($image_path, $filename, $item_id, $column_id)
    {
        // Get original dimensions of image
        list($width_orig, $height_orig) = getimagesize(
            Storage::disk('public')->path($image_path . $filename)
        );
        
        // Get the colmap holding the config for columns containing image dimensions
        $cm = Column::find($column_id)->column_mapping()->first();
        
        // Find the column holding the image width
        $width_column = $cm->getConfigValue('image_width_col');
        if ($width_column) {
            Detail::where('item_fk', $item_id)
            ->where('column_fk', $width_column)
            ->update(['value_int' => $width_orig]);
        }
        else {
            Log::info('No db column for image width.');
        }
        
        // Find the column holding the image height
        $height_column = $cm->getConfigValue('image_height_col');
        if ($height_column) {
            Detail::where('item_fk', $item_id)
            ->where('column_fk', $height_column)
            ->update(['value_int' => $height_orig]);
        }
        else {
            Log::info('No db column for image height.');
        }
    }

    /**
     * Handle resizing of a given image file.
     *
     * @param  string $image_path
     * @param  string $filename
     * @return void
     */
    public static function processImageResizing($image_path, $filename) {
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
    private static function createResizedImage($src_path, $filename, $width_orig, $height_orig, $dest_path, $width_thumb, $height_thumb) {
        // Calculate new dimensions
        $ratio = min($width_thumb/$width_orig, $height_thumb/$height_orig);
        $width_thumb = $width_orig * $ratio;
        $height_thumb = $height_orig * $ratio;
        
        // Load original image and scale it to new size
        $original = imagecreatefromjpeg(Storage::disk('public')->path($src_path . $filename));
        $scaled = imagescale($original, $width_thumb, $height_thumb, IMG_BICUBIC_FIXED);
        
        // Store thumbnail to disk
        imagejpeg($scaled, Storage::disk('public')->path($dest_path) . $filename);
    }
}
