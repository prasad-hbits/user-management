<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/users',[UsersController::class,'view_records']);

Route::post('/users',[UsersController::class,'add_record']);

Route::delete('/users',[UsersController::class,'delete_record']);

Route::put('/users',[UsersController::class,'update_record']);

Route::get('/users/{id}', [UsersController::class,'view_single_records']);
