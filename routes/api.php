<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SSEController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Ajoutez ces routes SSE
Route::middleware(['auth:sanctum'])->group(function () {
    // Connexion SSE
    Route::get('/events/stream', [SSEController::class, 'stream']);
    
    // Envoyer une notification
    Route::post('/events/notify', [SSEController::class, 'sendNotification']);
    
    // Statistiques des connexions
    Route::get('/events/connections', [SSEController::class, 'connections']);
    
    // Vérification de santé
    Route::get('/events/health', [SSEController::class, 'health']);
});

// Route publique pour le nettoyage (optionnel)
Route::post('/events/cleanup', [SSEController::class, 'cleanup']);