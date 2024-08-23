<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        try {
            // Llamar directamente al método index del ProductoController
            $categorias = Categoria::where('cat_estado', 1)->pluck('nombre', 'id');
            $productoController = new ProductoController();
            $response = $productoController->index();

            // Convertir la respuesta en un array si es necesario
            $productos = $response->getData(true);

            // Retornar la vista con los datos obtenidos
            return view('home', compact('productos', 'categorias'));
        } catch (\Exception $e) {
            return view('home', ['error' => $e->getMessage()]);
        }
    }
}
