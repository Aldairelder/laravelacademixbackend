<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        try {
            // Validar campos requeridos
            $request->validate([
                'usuario' => 'required|string',
                'password' => 'required|string',
            ]);

            $password = is_array($request->password) ? $request->password[0] : $request->password;

            // ✅ Buscar usuario con Eloquent
            $user = User::where('usuario', $request->usuario)
                        ->orWhere('email', $request->usuario)
                        ->with('rol') // si tienes una relación rol() definida en el modelo
                        ->first();

            // Validar credenciales
            if (!$user || !Hash::check($password, $user->password)) {
                return response()->json(['error' => 'Credenciales incorrectas'], 401);
            }

            // Crear token con Sanctum
            $token = $user->createToken('api-token')->plainTextToken;

            // Retornar respuesta exitosa
            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'nombre' => $user->nombre,
                    'apellido' => $user->apellido,
                    'usuario' => $user->usuario,
                    'email' => $user->email,
                    'genero' => $user->genero,
                    'rol_id' => $user->rol_id,
                    'rol' => $user->rol->nombre ?? null,
                ],
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en login: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error interno',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'message' => 'Sesión cerrada correctamente'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en logout: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al cerrar sesión',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
