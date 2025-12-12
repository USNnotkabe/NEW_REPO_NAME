<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\PetApiController;
use App\Http\Controllers\Api\AdoptionRequestApiController;
use App\Http\Controllers\Api\AdoptionHistoryApiController;
use App\Http\Controllers\Api\PetImageController;



// Public routes (no authentication required)
Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/login', [AuthApiController::class, 'login']);

// Public pet listing (anyone can view)
Route::get('/pets', [PetApiController::class, 'index']);
Route::get('/pets/{id}', [PetApiController::class, 'show']);

// Image serving route with CORS support
Route::get('/pet-image/{filename}', function ($filename) {
    // Sanitize filename to prevent directory traversal
    $filename = basename($filename);
    $path = storage_path('app/public/pets/' . $filename);
    
    if (!file_exists($path)) {
        abort(404, 'Image not found');
    }
    
    return response()->file($path, [
        'Content-Type' => mime_content_type($path),
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, OPTIONS',
        'Access-Control-Allow-Headers' => '*',
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->name('pet.image');

 Route::get('/pet-image/{filename}', [PetImageController::class, 'show'])->name('pet.image');

Route::middleware('auth:sanctum')->group(function () {

 
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/me', [AuthApiController::class, 'me']);

  
    Route::get('/my-pets', [PetApiController::class, 'myPets']);
    Route::post('/pets', [PetApiController::class, 'store']);
    Route::post('/pets/{id}', [PetApiController::class, 'update']); // POST because of image upload
    Route::delete('/pets/{id}', [PetApiController::class, 'destroy']);

  
    Route::get('/my-adoption-requests', [AdoptionRequestApiController::class, 'myRequests']);
    Route::post('/adoption-requests', [AdoptionRequestApiController::class, 'store']);
    Route::delete('/adoption-requests/{id}', [AdoptionRequestApiController::class, 'cancel']);

   
    Route::get('/my-pet-requests', [AdoptionRequestApiController::class, 'myPetRequests']);
    Route::post('/pet-requests/{id}/approve', [AdoptionRequestApiController::class, 'approve']);
    Route::post('/pet-requests/{id}/reject', [AdoptionRequestApiController::class, 'reject']);

    Route::get('/my-adoption-history', [AdoptionHistoryApiController::class, 'myHistory']);
   
});