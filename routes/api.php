<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;

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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group([
 
    'middleware' => 'api',
    'prefix' => 'auth'
 
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    //ruta para abmin
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    //ruta para usuario
    Route::post('/login_tienda', [AuthController::class, 'login_tienda'])->name('login_tienda');
    //ruta para trabajador
    Route::post('/login_trabajador', [AuthController::class, 'login_trabajador'])->name('login_trabajador');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->name('me');
});


//este codigo es para tener todas las rutas anteriores sin necesidad de estar volviendo a copiar y pegarlas
Route::group([
 
    'middleware' => 'api',
 
], function ($router) {
    Route::resource('/user', UserController::class);
});
