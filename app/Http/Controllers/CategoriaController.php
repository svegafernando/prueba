<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use \Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriaController extends Controller
{

    public function categoriaList()
    {
        try {
            // Obtener todas las categorías activas
            $categorias = Categoria::where('cat_estado', 1);

            // Obtener la respuesta de la API de categorías
            $response = app(CategoriaController::class)->index();

            // Convertir la respuesta en un array si es necesario
            $categoriasData = $response->getData(true);

            // Retornar la vista con los datos obtenidos
            return view('categorias.categorias', compact('categoriasData', 'categorias'));
        } catch (\Exception $e) {
            return view('categorias.categorias', ['error' => $e->getMessage()]);
        }
    }

/**
     * Listado de los registros de todas las categorías
     * @OA\Get (
     *     path="/api/categorias",
     *     tags={"Categorías"},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(
     *                     property="id",
     *                     type="number",
     *                     example="1"
     *                 ),
     *                 @OA\Property(
     *                     property="nombre",
     *                     type="string",
     *                     example="Ropa"
     *                 ),
     *                 @OA\Property(
     *                     property="descripcion",
     *                     type="string",
     *                     example="Todo tipo de ropas"
     *                 ),
     *                 @OA\Property(
     *                     property="categoria_id",
     *                     type="integer",
     *                     example="1",
     *                     description="ID de la categoría a la que pertenece el producto"
     *                 ),
     *                 @OA\Property(
     *                     property="created_at",
     *                     type="string",
     *                     example="2024-08-23T00:09:16.000000Z"
     *                 ),
     *                 @OA\Property(
     *                     property="updated_at",
     *                     type="string",
     *                     example="2024-08-23T12:33:45.000000Z"
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    public function index()
    {
        try {
            $results = Categoria::where('cat_estado', 1)->orderBy('id', 'desc')->get();

            return response()->json($results);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener la categoria: ' . $e->getMessage()], 500);
        }
    }

/**
 * Crear una nueva categoría
 * @OA\Post(
 *     path="/api/categorias",
 *     tags={"Categorías"},
 *     summary="Crea una nueva categoría",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="Categoria",
 *                 type="object",
 *                 required={"nombre"},
 *                 @OA\Property(
 *                     property="nombre",
 *                     type="string",
 *                     example="Electrónica",
 *                     description="Nombre de la categoría"
 *                 ),
 *                 @OA\Property(
 *                     property="descripcion",
 *                     type="string",
 *                     example="Artículos relacionados con electrónica",
 *                     description="Descripción de la categoría"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Categoría creada con éxito",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Categoria creada con éxito"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Errores de validación",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(
 *                     property="Categoria.nombre",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         example="El campo nombre es obligatorio."
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="No se pudo crear la categoria."
 *             )
 *         )
 *     )
 * )
 */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Categoria.nombre' => 'required|string|max:255',
            'Categoria.descripcion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            // Crear un nuevo producto
            $categoria = new Categoria();
            $categoria->nombre = $request->input('Categoria.nombre');
            $categoria->descripcion = $request->input('Categoria.descripcion');

            $categoria->save();

            DB::commit();

            return response()->json(['message' => 'Categoria creada con éxito'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'No se pudo crear la categoria.'], 500);
        }
    }

    /**
 * Mostrar una categoría específica
 * @OA\Get(
 *     path="/api/categorias/{id}",
 *     tags={"Categorías"},
 *     summary="Obtiene una categoría por su ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(
 *             type="integer",
 *             example=1,
 *             description="ID de la categoría"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Detalles de la categoría",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="id",
 *                 type="integer",
 *                 example=1
 *             ),
 *             @OA\Property(
 *                 property="nombre",
 *                 type="string",
 *                 example="Electrónica"
 *             ),
 *             @OA\Property(
 *                 property="descripcion",
 *                 type="string",
 *                 example="Artículos relacionados con electrónica"
 *             ),
 *             @OA\Property(
 *                 property="cat_estado",
 *                 type="integer",
 *                 example="1"
 *             ),
 *             @OA\Property(
 *                 property="created_at",
 *                 type="string",
 *                 format="date-time",
 *                 example="2024-08-23T00:09:16.000000Z"
 *             ),
 *             @OA\Property(
 *                 property="updated_at",
 *                 type="string",
 *                 format="date-time",
 *                 example="2024-08-23T12:33:45.000000Z"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="ID de categoría no válido",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="ID de categoria no válido"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Categoría no encontrada",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Categoria no encontrada"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Error interno del servidor"
 *             ),
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Detalles del error."
 *             )
 *         )
 *     )
 * )
 */

    public function show($id)
    {
        if (!is_numeric($id) || $id <= 0 || intval($id) != $id) {
            return response()->json(['message' => 'ID de categoria no válido'], 400);
        }

        try {
            $categoria = Categoria::where('id', $id)->where('cat_estado', 1)->first();

            if (!$categoria) {
                return response()->json(['message' => 'Categoria no encontrada'], 404);
            }

            return response()->json($categoria, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error interno del servidor', 'error' => $e->getMessage()], 500);
        }
    }

/**
 * Actualizar una categoría existente
 * @OA\Put(
 *     path="/api/categorias/{id}",
 *     tags={"Categorías"},
 *     summary="Actualiza una categoría por su ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(
 *             type="integer",
 *             example=1,
 *             description="ID de la categoría a actualizar"
 *         )
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="Categoria",
 *                 type="object",
 *                 @OA\Property(
 *                     property="nombre",
 *                     type="string",
 *                     example="Electrónica updated"
 *                 ),
 *                 @OA\Property(
 *                     property="descripcion",
 *                     type="string",
 *                     example="Artículos relacionados con electrónica updated"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Categoría actualizada con éxito",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Categoría actualizada con éxito"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Error de validación",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={
 *                     "Categoria.nombre": {
 *                         "El campo nombre es obligatorio."
 *                     }
 *                 }
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Categoría no encontrada",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Categoría no encontrada"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Error interno del servidor"
 *             ),
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Detalles del error."
 *             )
 *         )
 *     )
 * )
 */

    public function update(Request $request, $id)
    {
        // Validar la solicitud
        $validator = Validator::make($request->all(), [
            'Categoria.nombre' => 'required|string|max:255',
            'Categoria.descripcion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $categoria = Categoria::findOrFail($id);
            $categoria->nombre = $request->input('Categoria.nombre');
            $categoria->descripcion = $request->input('Categoria.descripcion');
            $categoria->save();

            DB::commit();

            return response()->json(['message' => 'Categoría actualizada con éxito'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'No se pudo actualizar la categoría.'], 500);
        }
    }

/**
 * Eliminar (marcar como inactivo) una categoría
 * @OA\Delete(
 *     path="/api/categorias/{id}",
 *     tags={"Categorías"},
 *     summary="Elimina una categoría por su ID (marca como inactivo)",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID de la categoría a eliminar",
 *         required=true,
 *         @OA\Schema(
 *             type="integer",
 *             example=1
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Categoría eliminada con éxito",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="mensaje",
 *                 type="string",
 *                 example="Categoría eliminada con éxito"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Categoría no encontrada",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="mensaje",
 *                 type="string",
 *                 example="Categoría no encontrada"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="mensaje",
 *                 type="string",
 *                 example="Error interno del servidor"
 *             ),
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Mensaje de error detallado"
 *             )
 *         )
 *     )
 * )
 */
    public function destroy(string $id)
    {
        try {
            $categoria = Categoria::find($id);

            if (!$categoria) {
                return response()->json(['mensaje' => 'Categoría no encontrada'], 404);
            }

            DB::beginTransaction();

            try {
                // Marcar el categoria como inactivo
                $categoria->cat_estado = 0;
                $categoria->save();

                DB::commit();

                return response()->json(['mensaje' => 'Categoría eliminada con éxito']);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['mensaje' => 'Error interno del servidor', 'error' => $e->getMessage()], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['mensaje' => 'Error interno del servidor', 'error' => $e->getMessage()], 500);
        }
    }
}
