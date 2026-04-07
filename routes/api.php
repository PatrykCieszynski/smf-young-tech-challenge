<?php

use App\Http\Controllers\Api\DocumentController;
use Illuminate\Support\Facades\Route;

Route::get('/documents', [DocumentController::class, 'index']);
Route::post('/documents', [DocumentController::class, 'store']);
Route::get('/documents/{document}', [DocumentController::class, 'show']);
Route::delete('/documents/{document}', [DocumentController::class, 'destroy']);
Route::post('/documents/{document}/process', [DocumentController::class, 'process']);
