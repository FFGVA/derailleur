<?php

use App\Http\Controllers\Api\EventRegistrationController;
use App\Http\Controllers\Api\FormController;
use App\Models\Event;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:form-submissions')->group(function () {
    Route::post('/contact', [FormController::class, 'contact']);
    Route::post('/adhesion', [FormController::class, 'adhesion']);
    Route::post('/inscription-event', [EventRegistrationController::class, 'store']);
});

Route::withoutMiddleware('throttle:api')->get('/events', function () {
    $events = Event::where('statuscode', 'P')
        ->where('starts_at', '>=', now()->startOfDay())
        ->whereNull('deleted_at')
        ->orderBy('starts_at')
        ->get();

    return response()->json($events->map(fn ($e) => [
        'id' => $e->id,
        'title' => $e->title,
        'description' => $e->description,
        'starts_at' => $e->starts_at->toIso8601String(),
        'ends_at' => $e->ends_at?->toIso8601String(),
        'location' => $e->location,
        'price' => $e->price,
        'price_non_member' => $e->price_non_member,
        'max_participants' => $e->max_participants,
    ]));
});
