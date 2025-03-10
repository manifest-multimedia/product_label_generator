<?php

use App\Http\Controllers\LabelController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LabelController::class, 'index'])->name('labels.index');
Route::post('/labels', [LabelController::class, 'store'])->name('labels.store');
Route::get('/labels/{id}', [LabelController::class, 'show'])->name('labels.show');
Route::put('/labels/{id}', [LabelController::class, 'update'])->name('labels.update');
Route::delete('/labels/{id}', [LabelController::class, 'destroy'])->name('labels.destroy');
Route::get('/labels/{id}/export', [LabelController::class, 'export'])->name('labels.export');
Route::get('/export-pdf', [LabelController::class, 'exportToPdf'])->name('labels.export.pdf');
Route::get('/search', [LabelController::class, 'search'])->name('labels.search'); // Changed to /search