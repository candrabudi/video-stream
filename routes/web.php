<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::get('/admin/login', [AuthController::class, 'index'])->name('viewLoginUser');
Route::get('/login', [AuthController::class, 'viewLoginUser'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomePageController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::prefix('videos')->name('videos.')->group(function () {
        Route::get('/', [VideoController::class, 'index'])->name('index');
        Route::get('/list', [VideoController::class, 'listData'])->name('listData');
        Route::get('/create', [VideoController::class, 'create'])->name('create');
        Route::post('/store', [VideoController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [VideoController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [VideoController::class, 'update'])->name('update');
        Route::post('/{id}/delete', [VideoController::class, 'destroy'])->name('destroy');
    });
    Route::get('/get-videos', [HomePageController::class, 'getVideos'])->name('getVideos');
    Route::get('/get-videos/{id}', [HomePageController::class, 'showVideo'])->name('homepage.video.show');
    Route::get('/search', [VideoController::class, 'search'])->name('videos.search');

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
});

Route::middleware(['auth', 'super_admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/list', [UserController::class, 'list'])->name('users.list');
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::post('/users/{id}/update', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}/delete', [UserController::class, 'destroy'])->name('users.delete');
});
