<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('usuario', 'password');
        $usuario = \DB::table('usuarios')->where('usuario', $credentials['usuario'])->first();
        if ($usuario && \Hash::check($credentials['password'], $usuario->password)) {
            $request->session()->put('usuario_id', $usuario->id);
            $request->session()->put('nombre', $usuario->nombre);
            $request->session()->put('rol_id', $usuario->rol_id);
            if ($usuario->rol_id == 1) {
                return redirect()->route('admin.panel');
            } elseif ($usuario->rol_id == 2) {
                return redirect()->route('profesor.cursos');
            } elseif ($usuario->rol_id == 3) {
                return redirect()->route('estudiante.panel');
            } else {
                // Puedes agregar más roles aquí
                return redirect()->route('login')->withErrors('Rol no permitido.');
            }
        }
        return redirect()->route('login')->withErrors('Credenciales incorrectas.');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('usuario_id');
        $request->session()->forget('nombre');
        return redirect()->route('login')->with('success', '¡Sesión cerrada exitosamente!');
    }

    public function showLoginForm()
    {
        return view('login');
    }
}
