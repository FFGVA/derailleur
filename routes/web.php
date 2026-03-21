<?php

use App\Http\Controllers\AdhesionActivationController;
use App\Http\Controllers\PortalAuthController;
use App\Http\Controllers\PortalController;
use Illuminate\Support\Facades\Route;

Route::get('/adhesion/confirmer', [AdhesionActivationController::class, 'confirm'])->name('adhesion.confirm');

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
    Route::get('/factures', [PortalController::class, 'factures'])->name('portail.factures');
    Route::get('/factures/{invoice}/pdf', [PortalController::class, 'facturePdf'])->name('portail.facture.pdf');
    Route::get('/peloton', [PortalController::class, 'peloton'])->name('portail.peloton');
    Route::get('/peloton/{event}', [PortalController::class, 'pelotonEvent'])->name('portail.peloton.event');
    Route::post('/peloton/{event}/presence/{targetMember}', [PortalController::class, 'togglePresence'])->name('portail.peloton.presence');
    Route::post('/peloton/{event}/ajouter', [PortalController::class, 'addParticipant'])->name('portail.peloton.add');
});

if (app()->environment('local')) {
    Route::view('/test-forms', 'test-forms');
}
