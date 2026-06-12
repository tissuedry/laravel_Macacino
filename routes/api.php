<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\HighlightController;
use App\Http\Controllers\Api\AIController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\ProfileController;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    Route::get('/stats', [StatsController::class, 'index']);
    
    // Documents
    Route::get('/documents', [DocumentController::class, 'index']);
    Route::post('/documents/upload', [DocumentController::class, 'upload']);
    Route::get('/documents/{id}', [DocumentController::class, 'show']);
    Route::get('/documents/{id}/download', [DocumentController::class, 'download']);
    Route::put('/documents/{id}/progress', [DocumentController::class, 'updateLastPage']);
    Route::put('/documents/{id}/total-pages', [DocumentController::class, 'updateTotalPages']);
    Route::delete('/documents/{id}', [DocumentController::class, 'destroy']);
    Route::post('/documents/bulk-delete', [DocumentController::class, 'bulkDestroy']);
    
    // Highlights
    Route::get('/documents/{id}/highlights', [HighlightController::class, 'getHighlights']);
    Route::post('/highlights', [HighlightController::class, 'createHighlight']);
    Route::post('/highlights/ai-note', [HighlightController::class, 'createAiNote']);
    Route::delete('/highlights/{id}', [HighlightController::class, 'destroy']);
    
    // AI
    Route::post('/ai/explain', [AIController::class, 'explainText']);
    Route::post('/ai/tts', [AIController::class, 'edgeTtsEndpoint']);
    
    // Profile
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword']);
});

Route::get('/test-headers', function (Request $request) {
    return response()->json([
        'headers' => $request->headers->all(),
        'has_auth' => $request->hasHeader('Authorization'),
        'auth_value' => $request->header('Authorization'),
    ]);
});
