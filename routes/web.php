<?php

use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/status', [UploadController::class, 'status']);
Route::post('/upload', [UploadController::class, 'upload']);
