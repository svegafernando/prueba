<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use \Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    public  $loginAfterSignUp = true;

    public function register(Request $request)
    {
        // Validación de los datos de entrada
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            // Creación del nuevo usuario
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            if ($this->loginAfterSignUp) {
                return $this->login($request);
            }

            return  response()->json([
                'status' => 'ok',
                'data' => $user
            ], 200);
        } catch (Exception $e) {
            // Manejar otros errores
            return response()->json([
                'error' => 'Error al registrar el usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    // Login de usuarios
    public function login(Request $request)
    {
        // Validar las credenciales recibidas
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['message' => 'Credenciales no válidas'], 400);
            }

            // Configurar la cookie con el token
            $cookie = Cookie::make(
                'access_token', // Nombre de la cookie
                $token,         // Valor de la cookie (el token)
                43200,             // Minutos de expiración (1 hora)
                '/',            // Path
                '127.0.0.1',           // Dominio (null para usar el dominio actual)
                false,           // Secure (true para HTTPS, false para HTTP)
                false,           // HttpOnly (true para no permitir acceso a JS)
                false,          // Raw (sin codificación especial)
                'None'        // SameSite (opciones: None, Lax, Strict)
            );

            // Devolver la respuesta con la cookie
            return response()->json([
                'status' => 'ok',
                'message' => 'Inicio de sesión exitoso'
            ])->cookie($cookie);
        } catch (JWTException $e) {
            return response()->json(['error' => 'No se pudo crear el token'], 500);
        }
    }
}
