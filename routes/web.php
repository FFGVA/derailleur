<?php

use App\Http\Controllers\AdhesionActivationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/adhesion/confirmer', [AdhesionActivationController::class, 'confirm'])->name('adhesion.confirm');

if (app()->environment('local')) {
    Route::view('/test-forms', 'test-forms');
}
