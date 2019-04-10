<?php

// Request an access token request, and a connectivity test.
Route::post('ping', PingController::class)->name('ping');

// Request a pre-check to the remote server.
Route::post('prechecks', PreChecksController::class)->name('prechecks');

// Uploads codebase (zip) to remote environment.
Route::post('upload', UploadController::class)->name('upload');

// Runs pre-push scripts.
Route::post('pre-scripts', PreScriptsController::class)->name('pre-scripts');

// Deploy (unzip) codebase into your remove environment.
Route::post('deploy', DeployController::class)->name('deploy');

// Runs post-push scripts.
Route::post('post-scripts', PostScriptsController::class)->name('post-scripts');
