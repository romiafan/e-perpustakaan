<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Inertia pages for frontend
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/catalog', [BookController::class, 'catalog'])->name('books.catalog');

    Route::get('/books/{id}/details', function ($id) {
        return Inertia::render('Books/Detail', ['bookId' => $id]);
    })->name('books.detail');

    Route::get('/my-reservations', function () {
        return Inertia::render('Reservations/Index');
    })->name('reservations.index');

    Route::get('/my-profile', function () {
        return Inertia::render('Profile/Index');
    })->name('profile.index');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';

// Contract JSON endpoints (could be moved to dedicated routes file if desired)
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{id}', [BookController::class, 'show']);

// Profile endpoints - handle auth manually to return 401 instead of redirect
Route::get('/profile', [ProfileController::class, 'show']);
Route::patch('/profile', [ProfileController::class, 'update']);

Route::middleware('auth')->group(function () {
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::patch('/reservations/{id}', [ReservationController::class, 'update']);
});
