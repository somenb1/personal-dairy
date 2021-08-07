<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DiaryController;
use App\Http\Controllers\PageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
}); */

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/diaries', [DiaryController::class, 'create']);
    Route::get('/diaries', [DiaryController::class, 'show']);
    Route::put('/diaries/{id}', [DiaryController::class, 'update']);

    Route::post('/pages', [PageController::class, 'create']);
    Route::get('/pages', [PageController::class, 'show']);
    Route::put('/pages/{id}', [PageController::class, 'update']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
