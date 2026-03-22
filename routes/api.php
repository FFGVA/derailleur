<?php

use App\Http\Controllers\Api\EventRegistrationController;
use App\Http\Controllers\Api\FormController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:form-submissions')->group(function () {
    Route::post('/contact', [FormController::class, 'contact']);
    Route::post('/adhesion', [FormController::class, 'adhesion']);
    Route::post('/inscription-event', [EventRegistrationController::class, 'store']);
});
