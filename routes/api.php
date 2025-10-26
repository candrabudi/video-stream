<?php

use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::get('/videos/search', [VideoController::class, 'apiSearch']);
