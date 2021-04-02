<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\PieceController;
use App\Http\Controllers\SaleController;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


//-------------------MODELO USUARIOS------------------------
Route::get('users', [UserController::class,'all']);
Route::get('users/{id}', [UserController::class,'show']);
Route::delete('/users/{id}', [UserController::class,'destroy']);
Route::put('/users/{id}', [UserController::class,'update']);
Route::post('/users', [UserController::class,'create']);

//-------------------MODELO MATERIALES------------------------
Route::get('materials', [MaterialController::class,'all']);
Route::get('materials/{id}', [MaterialController::class,'show']);
Route::delete('/materials/{id}', [MaterialController::class,'destroy']);
Route::put('/materials/{id}', [MaterialController::class,'update']);
Route::post('/materials', [MaterialController::class,'create']);

//-------------------MODELO PIEZAS------------------------
Route::get('pieces', [PieceController::class,'all']);
Route::get('pieces/{id}', [PieceController::class,'show']);
Route::delete('/pieces/{id}', [PieceController::class,'destroy']);
Route::put('/pieces/{id}', [PieceController::class,'update']);

//-------------------MODELO VENTAS------------------------
Route::get('sales', [SaleController::class,'all']);
Route::get('sales/{id}', [SaleController::class,'show']);
Route::delete('/sales/{id}', [SaleController::class,'destroy']);
Route::put('/sales/{id}', [SaleController::class,'update']);