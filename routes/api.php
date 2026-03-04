<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
// -- Functions Routes --
//Route::post('/invoke-agent', [AgentController::class, 'invoke'])->name('invoke-agent');
