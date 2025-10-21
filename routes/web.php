<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomePageController::class, 'index']);
Route::get('/get-videos', [HomePageController::class, 'getVideos'])->name('getVideos');
Route::get('/{id}', [HomePageController::class, 'showVideo'])->name('homepage.video.show');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// =====================
// PROTECTED ADMIN AREA
// =====================
Route::middleware(['auth'])->group(function () {
    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // VIDEOS
    Route::prefix('videos')->name('videos.')->group(function () {
        Route::get('/', [VideoController::class, 'index'])->name('index');
        Route::get('/list', [VideoController::class, 'listData'])->name('listData');
        Route::get('/create', [VideoController::class, 'create'])->name('create');
        Route::post('/store', [VideoController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [VideoController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [VideoController::class, 'update'])->name('update');
        Route::delete('/{id}/delete', [VideoController::class, 'destroy'])->name('destroy');
    });

    // CATEGORIES
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/list', [CategoryController::class, 'list'])->name('categories.list');
    Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('categories/store', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::post('categories/{id}/update', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{id}/delete', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::prefix('channels')->group(function () {
        Route::get('/', [ChannelController::class, 'index'])->name('channels.index');
        Route::get('/list', [ChannelController::class, 'listData'])->name('channels.list');
        Route::post('/store', [ChannelController::class, 'store'])->name('channels.store');
        Route::get('/{id}/edit', [ChannelController::class, 'edit'])->name('channels.edit');
        Route::post('/{id}/update', [ChannelController::class, 'update'])->name('channels.update');
        Route::delete('/{id}/delete', [ChannelController::class, 'destroy'])->name('channels.destroy');
    });

    // ANALYTICS / VIEWS
    Route::get('analytics/overview', [ViewController::class, 'overview'])->name('analytics.overview');
    Route::get('views', [ViewController::class, 'index'])->name('views.index');

    // SEO
    Route::get('seo', [SeoController::class, 'index'])->name('seo.index');
    Route::get('seo/{id}/edit', [SeoController::class, 'edit'])->name('seo.edit');
    Route::post('seo/{id}/update', [SeoController::class, 'update'])->name('seo.update');

    // USERS
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users/store', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::post('users/{id}/update', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{id}/delete', [UserController::class, 'destroy'])->name('users.destroy');

    // SETTINGS
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings/update', [SettingController::class, 'update'])->name('settings.update');

    // PROFILE
    Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('profile/update', [ProfileController::class, 'update'])->name('profile.update');
});
