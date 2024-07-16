<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;


Route::get('/', function () {
    return Inertia::render('Marketing/Home');
})->name('home');
Route::get('/team', function () {
    return Inertia::render('Marketing/Team');
})->name('team');

Route::get('/dashboard', function (Request $request) {
    if (!auth()->user()->public_key) {
        return redirect()->route('profile.keys');
    }
    $sortDir = 'asc';
    if ($request->has('sort')) {
        $request->validate([
            'sort' => 'in:file_name,size,created_at',
            'sort_dir' => 'required|boolean',
        ]);
        $sortDir = $request->sort_dir ? 'asc' : 'desc';
    }
    return Inertia::render('Dashboard/Dashboard', [
        'userFiles' => auth()->user()->media()->orderBy($request->sort ?? 'created_at', $sortDir)->paginate(10),
        'filesCount' => auth()->user()?->getMedia('*')->count(),
        'storageUsage' => auth()->user()?->getMedia('*')->sum('size'),
        'quota' => 3000000, // To be replaced with the user's quota based on the plan

    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/get-file', [FileController::class, 'get'])->name('get-file');
Route::patch('/rename-file', [FileController::class, 'modify'])->middleware(['auth', 'verified'])->name('rename-file');
Route::delete('/delete-file', [FileController::class, 'destroy'])->middleware(['auth', 'verified'])->name('delete-file');

Route::get('/account-data', function () {
    return Inertia::render('Dashboard/AccountData');
})->middleware(['auth', 'verified'])->name('account-data');

Route::get('/key-vault', function () {
    return Inertia::render('Dashboard/KeyVault', [
        'files' => auth()->user()->media()->paginate(10),
    ]);
})->middleware(['auth', 'verified'])->name('key-vault');

Route::post('/store-file', [\App\Http\Controllers\FileController::class, 'store'])->middleware(['auth', 'verified'])->name('store-file');

Route::middleware('auth')->group(function () {
    Route::get('/registerKeys', [ProfileController::class, 'getKeys'])->name('profile.keys');
    Route::get('/finalize', [ProfileController::class, 'finalize'])->name('finalize-reg');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
