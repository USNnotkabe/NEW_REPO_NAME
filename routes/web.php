<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdoptionRequestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PetListingController;

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

    // Adoption requests (for users)
    Route::get('/my-adoption-requests', [AdoptionRequestController::class, 'myRequests'])->name('adoption.my-requests');
    Route::get('/adopt/{pet}', [AdoptionRequestController::class, 'create'])->name('adoption.create');
    Route::post('/adopt', [AdoptionRequestController::class, 'store'])->name('adoption.store');
    Route::delete('/adoption-request/{id}/cancel', [AdoptionRequestController::class, 'cancel'])->name('adoption.cancel');
});

// Admin routes (only for admins)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/pets', [AdminController::class, 'pets'])->name('pets');
    Route::get('/adoption-requests', [AdminController::class, 'adoptionRequests'])->name('adoption-requests');

    // Adoption request actions
    Route::post('/adoption-request/{id}/approve', [AdminController::class, 'approveRequest'])->name('approve-request');
    Route::post('/adoption-request/{id}/reject', [AdminController::class, 'rejectRequest'])->name('reject-request');

    // User/Pet management
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('delete-user');
    Route::delete('/pets/{id}', [AdminController::class, 'deletePet'])->name('delete-pet');
});
