<?php

use App\Http\Controllers\ProductoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', 'App\Http\Controllers\AuthController@register');
Route::post('login', 'App\Http\Controllers\AuthController@login');

// Route::middleware('auth:api')->group(function () {
// Usamos los metodos del crud de esta manera
Route::resource('productos', 'App\Http\Controllers\ProductoController');
Route::resource('categorias', 'App\Http\Controllers\CategoriaController');
// });

// Route::get('/productos', [ProductoController::class, 'index'])->name('api.productos.index');
// Route::get('/productos/index', 'App\Http\Controllers\ProductoController@index');
// Route::post('/productos/store', 'App\Http\Controllers\ProductoController@store');
// Route::get('/productos/show/{id}', 'App\Http\Controllers\ProductoController@show');
// Route::put('/productos/update/{id}', 'App\Http\Controllers\ProductoController@update');
// Route::post('/productos/destroy/{id}', 'App\Http\Controllers\ProductoController@destroy');
// Route::resource('productos', 'App\Http\Controllers\ProductoController');

// Route::get('/categorias/index', 'App\Http\Controllers\CategoriaController@index');
// Route::post('/categorias/store', 'App\Http\Controllers\CategoriaController@store');
// Route::get('/categorias/show/{id}', 'App\Http\Controllers\CategoriaController@show');
// Route::put('/categorias/update/{id}', 'App\Http\Controllers\CategoriaController@update');
// Route::post('/categorias/destroy/{id}', 'App\Http\Controllers\CategoriaController@destroy');
// Route::resource('categorias', 'App\Http\Controllers\CategoriaController');
