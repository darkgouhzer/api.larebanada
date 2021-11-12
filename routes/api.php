<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ReposteraController;
use App\Http\Controllers\SucursaleController;
use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/register',[AuthController::class,'register']);

Route::post('/login',[AuthController::class,'login']);

Route::group(['middleware' => ['auth:sanctum']],function(){

    //Authentication and user updates
    Route::post('/logout', [AuthController::class,'logout']);
    Route::post('/update/name', [AuthController::class,'updatename']);
    Route::post('/update/password', [AuthController::class,'updatepassword']);

    //Productos
    Route::get('/unidades/medida', [ProductoController::class,'getUnidadesMedida']);
    Route::post('/producto', [ProductoController::class,'AltaProducto']);
    Route::post('/producto/eliminar', [ProductoController::class,'EliminarProducto']);
    Route::get('/producto/id/{id}', [ProductoController::class,'getProducto']);
    Route::get('/producto/nombre/{descripcion}', [ProductoController::class,'getProductos']);
    Route::get('/producto/nombre/{descripcion}/{page}/{pagesize}', [ProductoController::class,'getProductosPaginado']);

    //Sucursales
    Route::get('/sucursales', [SucursaleController::class,'getSucursales']);

    //Reposteras
    Route::post('/repostera', [ReposteraController::class,'AltaRepostera']);
    Route::post('/repostera/eliminar', [ReposteraController::class,'EliminarRepostera']);
    Route::get('/repostera/id/{id}', [ReposteraController::class,'getRepostera']);
    Route::get('/repostera/nombre/{descripcion}', [ReposteraController::class,'getReposteras']);

    //inventario
    Route::post('/entrada', [InventarioController::class,'Entrada']);
    Route::post('/salida', [InventarioController::class,'Salida']);
    Route::get('/entrada/obtener', [InventarioController::class,'getAllEntradas']);
    
    

});