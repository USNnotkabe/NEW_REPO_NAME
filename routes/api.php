<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\PetApiController;
use App\Http\Controllers\Api\AdoptionRequestApiController;
use App\Http\Controllers\Api\OwnerRequestApiController;
use App\Http\Controllers\Api\AdoptionHistoryApiController;
use App\Http\Controllers\AdminController;

// Public routes
Route::get('/pet-image/{filename}', [App\Http\Controllers\api\PetImageController::class, 'show']);
Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/login', [AuthApiController::class, 'login']);
Route::get('/pets', [PetApiController::class, 'index']);
Route::get('/pets/{id}', [PetApiController::class, 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/me', [AuthApiController::class, 'me']);

    Route::get('/my-pets', [PetApiController::class, 'myPets']);
    Route::post('/pets', [PetApiController::class, 'store']);
    Route::post('/pets/{id}', [PetApiController::class, 'update']);
    Route::delete('/pets/{id}', [PetApiController::class, 'destroy']);

    Route::get('/my-adoption-requests', [AdoptionRequestApiController::class, 'myRequests']);
    Route::post('/adoption-requests', [AdoptionRequestApiController::class, 'store']);
    Route::post('/adoption-requests', [AdoptionRequestApiController::class, 'store']);
    Route::get('/my-adoption-history', [AdoptionHistoryApiController::class, 'index']);

    Route::get('/my-pet-requests', [OwnerRequestApiController::class, 'index']);
    Route::post('/pet-requests/{id}/approve', [OwnerRequestApiController::class, 'approve']);
    Route::post('/pet-requests/{id}/reject', [OwnerRequestApiController::class, 'reject']);  // ✅ Only ONE reject route

    Route::prefix('admin')->group(function () {
        Route::get('/stats', [AdminController::class, 'apiGetStats']);
        Route::get('/users', [AdminController::class, 'apiGetUsers']);
        Route::get('/pets', [AdminController::class, 'apiGetPets']);
        Route::delete('/users/{id}', [AdminController::class, 'apiDeleteUser']);
    });
});
