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


Route::middleware(['auth', 'verified'])->group(function () {
    // -- Dashboard Route --
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    // --Candidates Route --
    Route::get('candidates', [CandidateController::class, 'index'])->name('candidate.index');
});


// -- Functions Routes --
Route::post('/agent', [AgentController::class, 'callAgent'])->name('invoke-agent');

require __DIR__ . '/settings.php';
