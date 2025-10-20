<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\OAuthController;
use App\Http\Controllers\API\ContextController;
use App\Http\Controllers\API\MemoryAdjustmentController;

Route::middleware('throttle:api')->group(function () {
    // OAuth
    Route::post('/oauth/token', [OAuthController::class, 'issue']);
    Route::post('/oauth/introspect', [OAuthController::class, 'introspect']);
    Route::post('/oauth/revoke', [OAuthController::class, 'revoke']);

    // Context service
    Route::get('/ai/{identity}/context', [ContextController::class, 'show']);
    Route::patch('/ai/{identity}/context', [ContextController::class, 'patch']);

    // Memory adjustments
    Route::post('/ai/{identity}/memory/adjustments', [MemoryAdjustmentController::class, 'create']);
    Route::get('/ai/{identity}/memory/adjustments/{adjustment}', [MemoryAdjustmentController::class, 'show']);
});