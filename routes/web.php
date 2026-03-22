<?php

use App\Enums\EventStatus;
use App\Http\Controllers\AdhesionActivationController;
use App\Http\Controllers\EventRegistrationController;
use App\Http\Controllers\PortalAuthController;
use App\Http\Controllers\PortalController;
use App\Models\Event;
use App\Services\ICalService;
use Illuminate\Support\Facades\Route;

Route::get('/adhesion/confirmer', [AdhesionActivationController::class, 'confirm'])->name('adhesion.confirm');

// Event registration (from website)
Route::get('/inscription-event/confirmer', [EventRegistrationController::class, 'confirmer'])
    ->middleware('throttle:10,1')
    ->name('inscription-event.confirmer');
Route::get('/inscription-event/nouveau', [EventRegistrationController::class, 'nouveauForm'])
    ->name('inscription-event.nouveau');
Route::post('/inscription-event/nouveau', [EventRegistrationController::class, 'nouveauStore'])
    ->middleware('throttle:5,1')
    ->name('inscription-event.nouveau.store');

Route::get('/events/ical', function () {
    $events = Event::whereIn('statuscode', [EventStatus::Publie, EventStatus::Termine])
        ->where('starts_at', '>=', now()->subYear())
        ->whereNull('deleted_at')
        ->orderBy('starts_at')
        ->get();

    return response(ICalService::generateFeed($events), 200, [
        'Content-Type' => 'text/calendar; charset=UTF-8',
    ]);
})->name('events.ical');

// Portal auth (public)
Route::get('/login', [PortalAuthController::class, 'login'])->name('portail.login');
Route::post('/auth/send-link', [PortalAuthController::class, 'sendLink'])
    ->middleware('throttle:5,1')
    ->name('portail.send-link');
Route::get('/auth/verify/{token}', [PortalAuthController::class, 'verifyToken'])
    ->middleware('throttle:10,1')
    ->where('token', '[a-f0-9]{64}')
    ->name('portail.verify');
Route::post('/deconnexion', [PortalAuthController::class, 'logout'])->name('portail.logout');

// Portal (authenticated)
Route::middleware('portal')->prefix('portail')->group(function () {
    Route::get('/', [PortalController::class, 'dashboard'])->name('portail.dashboard');
    Route::get('/adhesion', [PortalController::class, 'adhesion'])->name('portail.adhesion');
    Route::get('/adhesion/modifier', [PortalController::class, 'adhesionEdit'])->name('portail.adhesion.edit');
    Route::post('/adhesion/modifier', [PortalController::class, 'adhesionUpdate'])->name('portail.adhesion.update');
    Route::get('/factures', [PortalController::class, 'factures'])->name('portail.factures');
    Route::get('/evenement/{event}', [PortalController::class, 'evenement'])->name('portail.evenement');
    Route::post('/evenement/{event}/inscrire', [PortalController::class, 'inscrire'])->name('portail.evenement.inscrire');
    Route::post('/evenement/{event}/annuler', [PortalController::class, 'annuler'])->name('portail.evenement.annuler');
    Route::get('/evenement/{event}/ical', [PortalController::class, 'evenementIcal'])->name('portail.evenement.ical');
    Route::get('/factures/{invoice}/pdf', [PortalController::class, 'facturePdf'])->name('portail.facture.pdf');
    Route::get('/peloton', [PortalController::class, 'peloton'])->name('portail.peloton');
    Route::get('/peloton/{event}', [PortalController::class, 'pelotonEvent'])->name('portail.peloton.event');
    Route::post('/peloton/{event}/presence/{targetMember}', [PortalController::class, 'togglePresence'])->name('portail.peloton.presence');
    Route::post('/peloton/{event}/ajouter', [PortalController::class, 'addParticipant'])->name('portail.peloton.add');
    Route::post('/peloton/{event}/gpx', [PortalController::class, 'uploadGpx'])->name('portail.peloton.gpx');
    Route::get('/peloton/{event}/membre/{targetMember}', [PortalController::class, 'pelotonMember'])->name('portail.peloton.member');
});

if (app()->environment('local')) {
    Route::view('/test-forms', 'test-forms');
}
