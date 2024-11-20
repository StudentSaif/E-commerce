<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\website\WebsiteController;

Route::get('/', [WebsiteController::class, 'index'])->name('website');
