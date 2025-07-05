<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfesorApiController extends Controller
{
    private function getUsuarioId(Request $request)
    {
        $userEmail = $request->user()->email;
        $usuario = DB::table('usuarios')->where('email', $userEmail)->first();
        
        if (!$usuario) {
            return null;
        }
        
        return $usuario->id;
    }

    // === CURSOS ===
    public function cursos(Request $request)
    {
        $profesorId = $this->getUsuarioId($request);
        
        if (!$profesorId) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        $cursos = DB::table('curso_grupo')
            ->join('cursos', 'curso_grupo.curso_id', '=', 'cursos.id')
            ->join('grupos', 'curso_grupo.grupo_id', '=', 'grupos.id')
            ->where('curso_grupo.profesor_id', $profesorId)
            ->select('curso_grupo.*', 'cursos.nombre as curso', 'grupos.nombre as grupo')
            ->get();

        return response()->json($cursos);
    }

    public function detalleCurso($grupo_id, $curso_id)
    {
        $materiales = DB::table('materiales')
            ->where('grupo_id', $grupo_id)
            ->where('curso_id', $curso_id)->get();

        $evaluaciones = DB::table('evaluaciones')
            ->where('grupo_id', $grupo_id)
            ->where('curso_id', $curso_id)->get();

        $foros = DB::table('foros')
            ->where('grupo_id', $grupo_id)
            ->where('curso_id', $curso_id)->get();

        return response()->json([
            'materiales' => $materiales,
            'evaluaciones' => $evaluaciones,
            'foros' => $foros
        ]);
    }

    // === MATERIALES ===
    public function guardarMaterial(Request $request)
    {
        $id = DB::table('materiales')->insertGetId($request->only('grupo_id', 'curso_id', 'titulo', 'descripcion', 'archivo'));
        return response()->json(['id' => $id], 201);
    }

    public function actualizarMaterial(Request $request, $id)
    {
        DB::table('materiales')->where('id', $id)->update($request->only('titulo', 'descripcion', 'archivo'));
        return response()->json(['success' => true]);
    }

    public function eliminarMaterial($id)
    {
        DB::table('materiales')->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    // === EVALUACIONES ===
    public function guardarEvaluacion(Request $request)
    {
        $id = DB::table('evaluaciones')->insertGetId($request->only('grupo_id', 'curso_id', 'titulo', 'descripcion', 'fecha_entrega'));
        return response()->json(['id' => $id], 201);
    }

    public function actualizarEvaluacion(Request $request, $id)
    {
        DB::table('evaluaciones')->where('id', $id)->update($request->only('titulo', 'descripcion', 'fecha_entrega'));
        return response()->json(['success' => true]);
    }

    public function eliminarEvaluacion($id)
    {
        DB::table('evaluaciones')->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    // === FOROS ===
    public function guardarForo(Request $request)
    {
        $id = DB::table('foros')->insertGetId($request->only('grupo_id', 'curso_id', 'titulo', 'descripcion'));
        return response()->json(['id' => $id], 201);
    }

    public function actualizarForo(Request $request, $id)
    {
        DB::table('foros')->where('id', $id)->update($request->only('titulo', 'descripcion'));
        return response()->json(['success' => true]);
    }

    public function eliminarForo($id)
    {
        DB::table('foros')->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    // === ENTREGAS Y RESPUESTAS ===
    public function verEntregas($evaluacion_id)
    {
        $entregas = DB::table('entregas')
            ->join('users', 'entregas.estudiante_id', '=', 'users.id')
            ->where('evaluacion_id', $evaluacion_id)
            ->select('entregas.*', 'users.name')
            ->get();
        return response()->json($entregas);
    }

    public function verRespuestas($foro_id)
    {
        $respuestas = DB::table('respuestas')
            ->join('users', 'respuestas.estudiante_id', '=', 'users.id')
            ->where('foro_id', $foro_id)
            ->select('respuestas.*', 'users.name')
            ->get();
        return response()->json($respuestas);
    }

    // === NOTAS ===
    public function guardarNotaEntrega(Request $request, $evaluacion_id)
    {
        DB::table('entregas')
            ->where('evaluacion_id', $evaluacion_id)
            ->where('estudiante_id', $request->estudiante_id)
            ->update(['nota' => $request->nota]);

        return response()->json(['success' => true]);
    }

    public function guardarNotaRespuesta(Request $request, $foro_id)
    {
        DB::table('respuestas')
            ->where('foro_id', $foro_id)
            ->where('estudiante_id', $request->estudiante_id)
            ->update(['nota' => $request->nota]);

        return response()->json(['success' => true]);
    }

    // === ASISTENCIA ===
    public function verAsistencia($grupo_id, $curso_id)
    {
        $asistencias = DB::table('asistencias')
            ->where('grupo_id', $grupo_id)
            ->where('curso_id', $curso_id)->get();

        return response()->json($asistencias);
    }

    public function guardarAsistencia(Request $request, $grupo_id, $curso_id)
    {
        foreach ($request->asistencias as $asistencia) {
            DB::table('asistencias')->updateOrInsert(
                [
                    'grupo_id' => $grupo_id,
                    'curso_id' => $curso_id,
                    'estudiante_id' => $asistencia['estudiante_id'],
                    'fecha' => $asistencia['fecha']
                ],
                [
                    'estado' => $asistencia['estado']
                ]
            );
        }
        return response()->json(['success' => true]);
    }

    // === PERFIL ===
    public function verPerfil()
    {
        return response()->json(Auth::user());
    }

    public function actualizarPerfil(Request $request)
    {
        $datos = $request->only('name', 'email');

        if ($request->filled('password')) {
            $datos['password'] = Hash::make($request->password);
        }

        DB::table('users')->where('id', Auth::id())->update($datos);

        return response()->json(['success' => true]);
    }
}
