<?php

/*
|--------------------------------------------------------------------------
| Larapush routes
|--------------------------------------------------------------------------
|
| All routes that Larapush uses are declared on this file.
| They are all protected by the default Laravel Passport 'client' middleware
| and a custom middleware 'same-token' that validates additional token data.
|
*/

/*
|--------------------------------------------------------------------------
| Ping request
|--------------------------------------------------------------------------
|
| Requests and access token and checks if the web server have Larapush
| installed.
*/
Route::post('ping', PingController::class)->name('ping');

/*
|--------------------------------------------------------------------------
| Check web server environment
|--------------------------------------------------------------------------
|
| The web server verifies if all requirements are met to start an upload
| and codebase deployment process.
|
*/
Route::post('check-environment', CheckEnvironmentController::class)->name('pre-scripts');

/*
|--------------------------------------------------------------------------
| Pre-checks verification
|--------------------------------------------------------------------------
|
| The web server checks if it can write data in the storage path.
|
*/
Route::post('prechecks', PreChecksController::class)->name('prechecks');

/*
|--------------------------------------------------------------------------
| Web server codebase upload
|--------------------------------------------------------------------------
|
| Web server receives the codebase + scripts to run from your local
| computer.
|
*/
Route::post('upload', UploadController::class)->name('upload');

/*
|--------------------------------------------------------------------------
| Pre-deployment scripts execution
|--------------------------------------------------------------------------
|
| Request made to the web server to run the scripts prior to the codebase
| deployment.
|
*/
Route::post('pre-scripts', PreScriptsController::class)->name('pre-scripts');

/*
|--------------------------------------------------------------------------
| Deployment execution
|--------------------------------------------------------------------------
|
| The server deploys your code into your web server, meaning he unzips
| the received zip file, and verifies that all files were correctly
| extracted.
|
*/
Route::post('deploy', DeployController::class)->name('deploy');

/*
|--------------------------------------------------------------------------
| Post-deployment scripts execution
|--------------------------------------------------------------------------
|
| Request made to the web server to run the scripts after the codebase
| is successfully deployed.
|
*/
Route::post('post-scripts', PostScriptsController::class)->name('post-scripts');
