<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 動画アップロードフォーム表示
Route::get('/videos/upload', [VideoController::class, 'create'])->name('videos.create')->middleware('auth'); // 認証済みユーザーのみ

// 動画アップロード処理
Route::post('/videos', [VideoController::class, 'store'])->name('videos.store')->middleware('auth'); // 認証済みユーザーのみ

require __DIR__.'/auth.php';
