<?php

use FilamentSpatieLighthouse\Http\Controllers\LighthouseApiController;
use FilamentSpatieLighthouse\Http\Controllers\LighthouseReportController;
use Illuminate\Support\Facades\Route;

// HTML Report Routes (protected by Filament auth)
Route::middleware(['web'])->group(function () {
    Route::get('/lighthouse-reports/{id}', [LighthouseReportController::class, 'show'])
        ->name('filament-spatie-lighthouse.report.show');
    
    Route::get('/lighthouse-reports/{id}/download', [LighthouseReportController::class, 'download'])
        ->name('filament-spatie-lighthouse.report.download');
});

// API Routes (protected by secret token if enabled)
if (config('filament-spatie-lighthouse.endpoints.enabled', false)) {
    $prefix = config('filament-spatie-lighthouse.endpoints.prefix', 'lighthouse-api');
    
    Route::prefix($prefix)->name('filament-spatie-lighthouse.api.')->group(function () {
        Route::get('health', [LighthouseApiController::class, 'health'])->name('health');
        Route::get('latest', [LighthouseApiController::class, 'latest'])->name('latest');
        Route::get('latest/{url}', [LighthouseApiController::class, 'latest'])->name('latest.url');
        Route::get('results', [LighthouseApiController::class, 'index'])->name('results');
        Route::get('results/{id}', [LighthouseApiController::class, 'show'])->name('results.show');
    });
}
