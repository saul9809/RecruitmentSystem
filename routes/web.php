<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\LocaleController;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');
// -- Site Routes --

// -- Dashboard Route --
Route::get('dashboard', function () {
    return Inertia::render('dashboard');
})->middleware(['auth', 'verified'])
    ->name('dashboard');

// --Candidates Route --
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/candidates', [CandidateController::class, 'index'])->name('candidates.index');
});

// -- Functions Routes --
Route::post('/invoke-agent', [AgentController::class, 'invoke'])->name('invoke-agent');

// -- Locale Route --
Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');

require __DIR__ . '/settings.php';
