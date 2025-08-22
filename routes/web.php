<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'showForm'])->name('form');
Route::post('/entrar', [HomeController::class, 'entrar'])->name('entrar');
Route::get('/carta', [HomeController::class, 'carta'])->name('carta');
Route::get('/frase', [HomeController::class, 'showFrase'])->name('frase.show');
Route::post('/frase', [HomeController::class, 'frase'])->name('frase');