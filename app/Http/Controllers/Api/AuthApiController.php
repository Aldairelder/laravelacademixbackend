<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

            // Asegurar que password sea string (por si llega como array)
            $password = is_array($request->password) ? $request->password[0] : $request->password;

            // Buscar usuario por usuario o email
            $usuario = DB::table('usuarios')
                ->leftJoin('roles', 'usuarios.rol_id', '=', 'roles.id')
                ->select('usuarios.*', 'roles.nombre as rol_nombre')
                ->where('usuarios.usuario', $request->usuario)
                ->orWhere('usuarios.email', $request->usuario)
                ->first();

            \Log::info('Usuario encontrado:', (array) $usuario);
            \Log::info('Password recibido:', [$password]);

            // Validar credenciales
            if (!$usuario || !Hash::check($password, $usuario->password)) {
                \Log::error('Credenciales incorrectas');
                return response()->json(['error' => 'Credenciales incorrectas'], 401);
            }

            // Instanciar modelo User (asegúrate que apunte a tabla 'usuarios')
            $userModel = User::find($usuario->id);
            if (!$userModel) {
                \Log::error('No se encontró el modelo User para ID: ' . $usuario->id);
                return response()->json(['error' => 'Usuario no válido en el modelo'], 500);
            }

            // Crear token con Sanctum
            $token = $userModel->createToken('api-token')->plainTextToken;

            // Retornar respuesta exitosa
            return response()->json([
                'user' => [
                    'id' => $usuario->id,
                    'nombre' => $usuario->nombre,
                    'apellido' => $usuario->apellido,
                    'usuario' => $usuario->usuario,
                    'email' => $usuario->email,
                    'genero' => $usuario->genero,
                    'rol_id' => $usuario->rol_id,
                    'rol' => $usuario->rol_nombre,
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
