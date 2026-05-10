<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CandidateUploadController;
use App\Http\Controllers\CVProcessController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

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
// -- CV Process Route --
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/cv-process', [CVProcessController::class, 'index'])->name('cv-process.index');
});
// -- Candidate Upload Route --
Route::post('/cvs/upload', [CandidateUploadController::class, 'upload'])->name('cvs.upload');

// -- Functions Routes --
Route::post('/invoke-agent', [AgentController::class, 'invoke'])->name('invoke-agent');

// -- Locale Route --
Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');

// -- User  Routes --
Route::resource('users', UserController::class);
// -- Role  Routes --
Route::resource('roles', RoleController::class);

require __DIR__.'/settings.php';
