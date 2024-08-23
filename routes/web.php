<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CategoriaWebController;
use App\Http\Controllers\ProductoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('productos', ProductoController::class);
// Route::resource('categorias', CategoriaWebController::class);

Route::get('/categorias', [CategoriaController::class, 'categoriaList'])->name('categorias.categorias');

Route::resource('categorias', CategoriaController::class)->except(['index']);
