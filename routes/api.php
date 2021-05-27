<?php
/*Fichero de rutas para crear la Api accediendo
  a los diferentes métodos de los diferentes modelos*/


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MessageController;
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
//---------------Rutas de registro y login------------------

Route::post('register', [PassportAuthController::class, 'register']);
Route::post('login', [PassportAuthController::class, 'login']);
Route::post('password', [PassportAuthController::class, 'recuperate']);

// Route::middleware('auth:api')->group(function () {
//     Route::get('users', [UserController::class,'all']);
// });

//-------------------MODELO USUARIOS------------------------
Route::get('users', [UserController::class,'all'])->middleware('auth:api');
Route::get('users/{id}', [UserController::class,'show'])->middleware('auth:api');
Route::delete('/users/{id}', [UserController::class,'destroy'])->middleware('auth:api');
Route::put('/users/{id}', [UserController::class,'update'])->middleware('auth:api');
Route::post('/users', [UserController::class,'create'])->middleware('auth:api');
Route::get('users/profile/{id}', [UserController::class,'showProfile'])->middleware('auth:api');
Route::get('avatar/{id}', [UserController::class,'getImage']);

//Por el tema de la img, aqui se pone mediante post para que reciba el form con el header.
//Para que funcione con put habría que instalar otra libreria de la que no tengo conocimiento.
Route::post('/users/profile/{id}', [UserController::class,'updateProfile'])->middleware('auth:api');

//-------------------MODELO MATERIALES------------------------
Route::get('materials', [MaterialController::class,'all'])->middleware('auth:api');
Route::get('materials/{id}', [MaterialController::class,'show'])->middleware('auth:api');
Route::delete('/materials/{id}', [MaterialController::class,'destroy'])->middleware('auth:api');
Route::put('/materials/{id}', [MaterialController::class,'update'])->middleware('auth:api');
Route::post('/materials', [MaterialController::class,'create'])->middleware('auth:api');

//-------------------MODELO PIEZAS------------------------
Route::get('pieces', [PieceController::class,'all']);
Route::get('pieces/detail/{id}', [PieceController::class,'detail']);//Para ver en detalle
Route::get('pieces/{id}', [PieceController::class,'show'])->middleware('auth:api');
Route::delete('/pieces/{id}', [PieceController::class,'destroy'])->middleware('auth:api');
Route::get('img/{id}', [PieceController::class,'getImage']);
Route::get('addSale/{id}', [PieceController::class,'addSale'])->middleware('auth:api');
// Route::put('/changeSold/{id}', [PieceController::class,'changeSold'])->middleware('auth:api');

//Por el tema de la img, aqui se pone mediante post para que reciba el form con el header.
//Para que funcione con put habría que instalar otra libreria de la que no tengo conocimiento.
Route::post('/pieces/{id}', [PieceController::class,'update'])->middleware('auth:api');
Route::post('/mypieces/{idUser}/{id}', [PieceController::class,'updateMyPieces'])->middleware('auth:api');

Route::get('mypieces/{idUser}', [PieceController::class,'allMyPieces'])->middleware('auth:api');
Route::delete('/mypieces/{idUser}/{id}', [PieceController::class,'destroyMyPieces'])->middleware('auth:api');
Route::get('mypieces/{idUser}/{id}', [PieceController::class,'showMyPieces'])->middleware('auth:api');
Route::get('newmypiece/{idUser}', [PieceController::class,'newMyPiece'])->middleware('auth:api');
Route::post('/newmypiece/{idUser}', [PieceController::class,'createMyPiece'])->middleware('auth:api');
Route::get('addSale/{idUser}/{id}', [PieceController::class,'addMySale'])->middleware('auth:api');


//-------------------MODELO VENTAS------------------------
Route::get('sales', [SaleController::class,'all']);
Route::get('sales/{id}', [SaleController::class,'show'])->middleware('auth:api');
Route::put('/sales/{id}', [SaleController::class,'update'])->middleware('auth:api');
Route::delete('/sales/{id}', [SaleController::class,'destroy'])->middleware('auth:api');
Route::post('/addsale/{id}', [SaleController::class,'create'])->middleware('auth:api');
Route::get('mysales/{idUser}', [SaleController::class,'allMySales'])->middleware('auth:api');
Route::get('mysales/{idUser}/{id}', [SaleController::class,'showMySale'])->middleware('auth:api');
Route::put('/mysales/{idUser}/{id}', [SaleController::class,'updateMySale'])->middleware('auth:api');
Route::delete('/mysales/{idUser}/{id}', [SaleController::class,'destroyMySale'])->middleware('auth:api');
Route::post('/addmysale/{idUser}/{id}', [SaleController::class,'createMySale'])->middleware('auth:api');

//-------------------MODELO MENSSAGE------------------------
Route::get('messages/received/{idUser}', [MessageController::class,'allMsgReceived'])->middleware('auth:api');
Route::get('messages/sended/{idUser}', [MessageController::class,'allMsgSended'])->middleware('auth:api');
Route::get('messages/{idUser}/{id}', [MessageController::class,'show'])->middleware('auth:api');
Route::post('/messages/delete/{idUser}', [MessageController::class,'destroy'])->middleware('auth:api');