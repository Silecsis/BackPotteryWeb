<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\PieceController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PassportAuthController;

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


//---------------------AUTH PASSPORT------------------------

Route::post('register', [PassportAuthController::class, 'register']);
Route::post('login', [PassportAuthController::class, 'login']);

// Route::middleware('auth:api')->group(function () {
//     Route::get('users', [UserController::class,'all']);
// });

// -------------------MODELO USUARIOS------------------------
Route::get('users', [UserController::class,'all'])->middleware('auth:api');
Route::get('users/{id}', [UserController::class,'show'])->middleware('auth:api');
Route::delete('/users/{id}', [UserController::class,'destroy'])->middleware('auth:api');
Route::put('/users/{id}', [UserController::class,'update'])->middleware('auth:api');
Route::post('/users', [UserController::class,'create'])->middleware('auth:api');
Route::post('/users/{id}', [UserController::class,'updateProfile'])->middleware('auth:api');
Route::get('users/profile/{id}', [UserController::class,'showProfile'])->middleware('auth:api');

//-------------------MODELO MATERIALES------------------------
Route::get('materials', [MaterialController::class,'all'])->middleware('auth:api');
Route::get('materials/{id}', [MaterialController::class,'show'])->middleware('auth:api');
Route::delete('/materials/{id}', [MaterialController::class,'destroy'])->middleware('auth:api');
Route::put('/materials/{id}', [MaterialController::class,'update'])->middleware('auth:api');
Route::post('/materials', [MaterialController::class,'create'])->middleware('auth:api');

//-------------------MODELO PIEZAS------------------------
Route::get('pieces', [PieceController::class,'all']);
Route::get('pieces/{id}', [PieceController::class,'show']);
Route::delete('/pieces/{id}', [PieceController::class,'destroy'])->middleware('auth:api');
Route::put('/pieces/{id}', [PieceController::class,'update'])->middleware('auth:api');

//-------------------MODELO VENTAS------------------------
Route::get('sales', [SaleController::class,'all']);
Route::get('sales/{id}', [SaleController::class,'show'])->middleware('auth:api');
Route::delete('/sales/{id}', [SaleController::class,'destroy'])->middleware('auth:api');
Route::put('/sales/{id}', [SaleController::class,'update'])->middleware('auth:api');