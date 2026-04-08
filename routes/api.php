<?php

use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::get('/documents', [DocumentController::class, 'index']);
Route::post('/documents', [DocumentController::class, 'store']);
Route::get('/documents/{document}', [DocumentController::class, 'show']);
Route::patch('/documents/{document}', [DocumentController::class, 'update']);
Route::delete('/documents/{document}', [DocumentController::class, 'destroy']);
Route::post('/documents/{document}/process', [DocumentController::class, 'process']);

Route::get('/invoices', [InvoiceController::class, 'index']);
Route::get('/invoices/{invoice}', [InvoiceController::class, 'show']);
