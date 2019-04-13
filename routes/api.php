<?php

/**
 * Larapush API routes.
 * All routes are protected using the 'client' Laravel Passport middleware
 * to guarantee transaction security.
 *
 * Additionally they are also protected by the Larapush 'same-token' middleware meaning a custom token
 * needs to be the same in the local dev computer and in the web server.
 */

// Request an access token request, and a connectivity test.
Route::post('ping', PingController::class)->name('ping');

// Runs environment check.
Route::post('check-environment', CheckEnvironmentController::class)->name('pre-scripts');

// Request a pre-check to the web server.
Route::post('prechecks', PreChecksController::class)->name('prechecks');

// Uploads codebase (zip) to remote environment.
Route::post('upload', UploadController::class)->name('upload');

// Runs pre-push scripts.
Route::post('pre-scripts', PreScriptsController::class)->name('pre-scripts');

// Deploy (unzip) codebase into your remove environment.
Route::post('deploy', DeployController::class)->name('deploy');

// Runs post-push scripts.
Route::post('post-scripts', PostScriptsController::class)->name('post-scripts');
