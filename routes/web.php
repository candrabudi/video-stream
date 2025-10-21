<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomePageController::class, 'index']);
Route::get('/get-videos', [HomePageController::class, 'getVideos'])->name('getVideos');
Route::get('/get-videos/{id}', [HomePageController::class, 'showVideo'])->name('homepage.video.show');
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('videos')->name('videos.')->group(function () {
        Route::get('/', [VideoController::class, 'index'])->name('index');
        Route::get('/list', [VideoController::class, 'listData'])->name('listData');
        Route::get('/create', [VideoController::class, 'create'])->name('create');
        Route::post('/store', [VideoController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [VideoController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [VideoController::class, 'update'])->name('update');
        Route::delete('/{id}/delete', [VideoController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/list', [CategoryController::class, 'list'])->name('list');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/store', [CategoryController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{id}/delete', [CategoryController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('channels')->name('channels.')->group(function () {
        Route::get('/', [ChannelController::class, 'index'])->name('index');
        Route::get('/list', [ChannelController::class, 'listData'])->name('list');
        Route::post('/store', [ChannelController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ChannelController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [ChannelController::class, 'update'])->name('update');
        Route::delete('/{id}/delete', [ChannelController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/overview', [HomePageController::class, 'overview'])->name('overview');
    });

    Route::get('views', [HomePageController::class, 'index'])->name('views.index');

    Route::prefix('seo')->name('seo.')->group(function () {
        Route::get('/', [HomePageController::class, 'index'])->name('index');
        Route::get('/{id}/edit', [HomePageController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [HomePageController::class, 'update'])->name('update');
    });

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [HomePageController::class, 'index'])->name('index');
        Route::get('/create', [HomePageController::class, 'create'])->name('create');
        Route::post('/store', [HomePageController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [HomePageController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [HomePageController::class, 'update'])->name('update');
        Route::delete('/{id}/delete', [HomePageController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [HomePageController::class, 'index'])->name('index');
        Route::post('/update', [HomePageController::class, 'update'])->name('update');
    });

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [HomePageController::class, 'index'])->name('index');
        Route::post('/update', [HomePageController::class, 'update'])->name('update');
    });
});
