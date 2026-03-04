<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PromptTestController;

// Healthcheck simple
Route::get('/ping', fn() => response()->json(['pong' => true]));

// Versión 1 del API (recomendado para evolución)
Route::prefix('v1')->group(function () {

    // Público (sin auth) — solo ejemplo
    Route::get('/status', fn() => ['ok' => true, 'version' => 1]);

    // Rutas protegidas con Sanctum (Bearer token o session cookie stateful)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', fn(Request $request) => $request->user());

        // CRUD de candidatos (ejemplos)
        Route::get('/test',       [PromptTestController::class, 'testAgent']);
    });
});
