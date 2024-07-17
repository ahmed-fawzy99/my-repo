<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Home
Route::get('/', function () {
    return Inertia::render('Marketing/Home');
})->name('home');

Route::get('/team', function () {
    return Inertia::render('Marketing/Team');
})->name('team');

// Sharing
Route::get('/get-file', [FileController::class, 'get'])->name('get-file');

// Dashboard
Route::group(['middleware' => ['auth', 'verified']], function () {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('key-vault', [DashboardController::class, 'vault'])->name('key-vault');

    // Files
    Route::post('store-file', [FileController::class, 'store'])->name('store-file');
    Route::patch('rename-file', [FileController::class, 'modify'])->name('rename-file');
    Route::delete('delete-file', [FileController::class, 'destroy'])->name('delete-file');

    // Contacts
    Route::get('contacts/requests', [ContactController::class, 'requestsIndex'])->name('contacts-requests');
    Route::resource('contacts', ContactController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('account-data', [ProfileController::class, 'show'])->name('account-data');
    Route::get('register-keys', [ProfileController::class, 'getKeys'])->name('register-keys');
    Route::get('finalize', [ProfileController::class, 'finalize'])->name('finalize-reg');
});

require __DIR__ . '/auth.php';
