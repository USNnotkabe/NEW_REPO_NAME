<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\PetApiController;
use App\Http\Controllers\Api\AdoptionRequestApiController;
use App\Http\Controllers\Api\OwnerRequestApiController;
use App\Http\Controllers\Api\AdoptionHistoryApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes (no authentication required)
Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/login', [AuthApiController::class, 'login']);

// Public pet listing (anyone can view)
Route::get('/pets', [PetApiController::class, 'index']);
Route::get('/pets/{id}', [PetApiController::class, 'show']);

// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {

    // Auth routes
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/me', [AuthApiController::class, 'me']);

    // Pet management (user's own pets)
    Route::get('/my-pets', [PetApiController::class, 'myPets']);
    Route::post('/pets', [PetApiController::class, 'store']);
    Route::post('/pets/{id}', [PetApiController::class, 'update']); // POST because of image upload
    Route::delete('/pets/{id}', [PetApiController::class, 'destroy']);

    // Adoption requests (for users wanting to adopt/buy)
    Route::get('/my-adoption-requests', [AdoptionRequestApiController::class, 'myRequests']);
    Route::post('/adoption-requests', [AdoptionRequestApiController::class, 'store']);
    Route::delete('/adoption-requests/{id}', [AdoptionRequestApiController::class, 'cancel']);

    // Owner request management (for pet owners to approve/reject)
    Route::get('/my-pet-requests', [OwnerRequestApiController::class, 'index']);
    Route::post('/pet-requests/{id}/approve', [OwnerRequestApiController::class, 'approve']);
    Route::post('/pet-requests/{id}/reject', [OwnerRequestApiController::class, 'reject']);

    // Adoption history
    Route::get('/my-adoption-history', [AdoptionHistoryApiController::class, 'index']);
});
