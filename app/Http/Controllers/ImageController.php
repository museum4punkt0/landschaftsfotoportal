<?php

namespace App\Http\Controllers;

use App\ModuleInstance;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * Get a random image from storage and send to browser.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getRandom(Request $request)
    {
        $image_module = ModuleInstance::getByName($request->query('module'));

        // Provide a invalid path if config option doesn't exist
        $directory = $image_module->config['image_path'] ?? 'not_existing_directory';

        // Get all files from directory
        $files = Storage::disk('public')->files($directory);

        if (!$files) {
            return response()->json(['error' => 'no image files found'], 404);
        }
        // Get file path of one random file
        $path = Storage::disk('public')->path(Arr::random($files));

        return response()->file($path);
    }
}
