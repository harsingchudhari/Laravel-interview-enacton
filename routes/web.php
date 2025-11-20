<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrizeProbabilitiesController;
// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [PrizeProbabilitiesController::class, 'index'])->name('prizee.index');
Route::get('create',[PrizeProbabilitiesController::class,'create'])->name('prizee.create');
Route::post('store', [PrizeProbabilitiesController::class, 'store'])->name('prizee.store');
Route::get('products/edit/{id}', [PrizeProbabilitiesController::class, 'edit'])->name('prizee.edit');
Route::post('prizee/update/{id}', [PrizeProbabilitiesController::class, 'update'])->name('prizee.update');
Route::delete('products/delete/{id}', [PrizeProbabilitiesController::class, 'destroy'])->name('prizee.delete');

// prize probabilities