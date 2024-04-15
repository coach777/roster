<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use LaravelJsonApi\Laravel\Facades\JsonApiRoute;
use LaravelJsonApi\Laravel\Http\Controllers\JsonApiController;

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

//Route::post('uploading-file-api', [FileUploadController::class, 'upload']);
Route::post('/roster/upload', 'App\Http\Controllers\RosterFile@upload');

JsonApiRoute::server('v1')->resources(function ($server) {
    $server->resource('activities', JsonApiController::class)->readOnly();
});
