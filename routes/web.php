<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\AgentController;

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
Route::get('candidates', [CandidateController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('candidate.index');

// -- Functions Routes --
Route::post('/invoke-agent', [AgentController::class, 'invoke'])->name('invoke-agent');

require __DIR__ . '/settings.php';
