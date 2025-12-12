<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class PetImageController extends Controller
{
    public function show($filename)
    {
        $filename = basename($filename);
        $path = storage_path('app/public/pets/' . $filename);
        
        if (!file_exists($path)) {
            abort(404);
        }
        
        return response()->file($path, [
            'Content-Type' => mime_content_type($path),
            'Access-Control-Allow-Origin' => '*',
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}