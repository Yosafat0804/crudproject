<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// route dengan mode resources
Route::resource('/products', ProductController::class);

Route::get('/', function () {
    return view('welcome');
});
