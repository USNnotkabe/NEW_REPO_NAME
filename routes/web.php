<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdoptionRequestController;
use App\Http\Controllers\AdoptionHistoryController;
use App\Http\Controllers\OwnerRequestController;  // ← ADD THIS!
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PetListingController;Route::get('/storage/valid_ids/{filename}', function ($filename) {
    $path = storage_path('app/public/valid_ids/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path, [
        'Access-Control-Allow-Origin' => '*',
        'Content-Type' => 'image/jpeg',
    ]);
})->where('filename', '.*');


// Welcome page
Route::get('/', function () {
    return view('welcome');
});

// Public auth routes
Route::get('register', [AuthController::class, 'showRegister'])->name('show.register');
Route::get('login', [AuthController::class, 'showLogin'])->name('show.login');
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');

// Public pet listing (anyone can view)
Route::get('/petlisting', [PetListingController::class, 'index'])->name('petlisting.index');
Route::get('/petlisting/{id}', [PetListingController::class, 'show'])->name('petlisting.show');

// Protected routes (must be logged in)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // User routes
    Route::resource('pets', PetController::class);

    // Adoption requests (for users wanting to adopt/buy)
    Route::get('/my-adoption-requests', [AdoptionRequestController::class, 'myRequests'])->name('adoption.my-requests');  // ← ADD THIS!
    Route::get('/adopt/{pet}', [AdoptionRequestController::class, 'create'])->name('adoption.create');
    Route::post('/adopt', [AdoptionRequestController::class, 'store'])->name('adoption.store');
    Route::delete('/adoption-request/{id}/cancel', [AdoptionRequestController::class, 'cancel'])->name('adoption.cancel');

    // Adoption history
    Route::get('/my-adoption-history', [AdoptionHistoryController::class, 'index'])->name('adoption.history');

    // Owner request management (NEW! - for pet owners to approve/reject)
    Route::get('/my-pet-requests', [OwnerRequestController::class, 'index'])->name('owner.requests');
    Route::post('/pet-request/{id}/approve', [OwnerRequestController::class, 'approve'])->name('owner.approve-request');
    Route::post('/pet-request/{id}/reject', [OwnerRequestController::class, 'reject'])->name('owner.reject-request');
});

// Admin routes (only for admins - monitoring only)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/pets', [AdminController::class, 'pets'])->name('pets');

    // User/Pet management
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('delete-user');

    Route::get('/adopt/{pet}', [AdoptionRequestController::class, 'create'])->name('adoption.create');
});
