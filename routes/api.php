<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PetListingController;
use App\Http\Controllers\AuthController;

// Test route (public)
Route::get('/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'API is working!',
    ]);
});

// Public auth routes
Route::post('/register', [AuthController::class, 'apiRegister']);
Route::post('/login', [AuthController::class, 'apiLogin']);

// Public read-only pet routes
Route::get('/petlisting', [PetListingController::class, 'index']);
Route::get('/petlisting/{id}', [PetListingController::class, 'show']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'apiLogout']);
    Route::post('/petlisting', [PetListingController::class, 'store']);
    Route::put('/petlisting/{id}', [PetListingController::class, 'update']);
    Route::delete('/petlisting/{id}', [PetListingController::class, 'destroy']);
});
