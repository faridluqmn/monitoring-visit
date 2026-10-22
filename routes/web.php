<?php

use App\Http\Controllers\VisitController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('visits.index');
});

Route::get('/visits', [VisitController::class, 'index'])->name('visits.index');
Route::get('/visits/create', [VisitController::class, 'create'])->name('visits.create');
Route::post('/visits', [VisitController::class, 'store'])->name('visits.store');
