<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User; // Asegúrate que este modelo apunte a la tabla 'usuarios'

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'usuario' => 'required|string',
                'password' => 'required|string',
            ]);

            // Buscar usuario
            $usuario = DB::table('usuarios')
                ->leftJoin('roles', 'usuarios.rol_id', '=', 'roles.id')
                ->select('usuarios.*', 'roles.nombre as rol_nombre')
                ->where('usuarios.usuario', $request->usuario)
                ->orWhere('usuarios.email', $request->usuario)
                ->first();

            \Log::info('Usuario encontrado:', (array) $usuario);
            \Log::info('Request password:', [$request->password]);

            if (!$usuario || !Hash::check($request->password, $usuario->password)) {
                \Log::error('Credenciales incorrectas');
                throw ValidationException::withMessages([
                    'usuario' => ['Las credenciales proporcionadas son incorrectas.'],
                ]);
            }

            // Obtener instancia del modelo User (asegúrate que App\Models\User use la tabla 'usuarios')
            $userModel = User::find($usuario->id);

            if (!$userModel) {
                return response()->json(['error' => 'Usuario no válido en el modelo'], 500);
            }

            // Crear token
            $token = $userModel->createToken('api-token')->plainTextToken;

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
                // 'trace' => $e->getTraceAsString(), // ⚠️ Solo para depuración, no en producción
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente'
        ]);
    }
}
