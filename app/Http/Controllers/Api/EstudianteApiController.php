<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EstudianteApiController extends Controller
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

    public function cursos(Request $request)
    {
        $estudiante_id = $this->getUsuarioId($request);
        
        if (!$estudiante_id) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        $cursos = DB::table('matriculas as m')
            ->join('grupos as g', 'm.grupo_id', '=', 'g.id_grupo')
            ->join('asignaciones as a', 'a.grupo_id', '=', 'g.id_grupo')
            ->join('cursos as c', 'a.curso_id', '=', 'c.id_curso')
            ->where('m.estudiante_id', $estudiante_id)
            ->select('c.id_curso', 'c.nombre as curso', 'g.id_grupo', 'g.nombre as grupo')
            ->distinct()
            ->get();
        return response()->json($cursos);
    }

    public function detalleCurso(Request $request, $grupo_id, $curso_id)
    {
        $estudiante_id = $this->getUsuarioId($request);
        
        if (!$estudiante_id) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $matricula = DB::table('matriculas')
            ->where('estudiante_id', $estudiante_id)
            ->where('grupo_id', $grupo_id)
            ->first();
        if (!$matricula) return response()->json(['error' => 'No autorizado'], 403);

        $grupo = DB::table('grupos')
            ->join('asignaciones', 'asignaciones.grupo_id', '=', 'grupos.id_grupo')
            ->join('cursos', 'asignaciones.curso_id', '=', 'cursos.id_curso')
            ->where('grupos.id_grupo', $grupo_id)
            ->where('cursos.id_curso', $curso_id)
            ->select('grupos.nombre as grupo', 'cursos.nombre as curso')
            ->first();

        $materiales = DB::table('materiales')
            ->where('grupo_id', $grupo_id)
            ->where('curso_id', $curso_id)
            ->orderByDesc('fecha_subida')
            ->get();

        $evaluaciones = DB::table('evaluaciones')
            ->where('grupo_id', $grupo_id)
            ->where('curso_id', $curso_id)
            ->orderByDesc('fecha')
            ->get();

        $foros = DB::table('foro')
            ->where('grupo_id', $grupo_id)
            ->where('curso_id', $curso_id)
            ->orderByDesc('fecha_publicacion')
            ->get();

        $notas = DB::table('notas')
            ->where('estudiante_id', $estudiante_id)
            ->get()
            ->keyBy(fn($n) => $n->tipo . '_' . $n->origen_id);

        $entregas = DB::table('entregas')
            ->where('estudiante_id', $estudiante_id)
            ->get()
            ->keyBy('evaluacion_id');

        $respuestas_foro = DB::table('respuestas_foro')
            ->join('usuarios', 'usuarios.id', '=', 'respuestas_foro.estudiante_id')
            ->select('respuestas_foro.*', 'usuarios.nombre', 'usuarios.apellido')
            ->get();

        return response()->json(compact(
            'grupo', 'materiales', 'evaluaciones', 'foros', 'notas', 'entregas', 'respuestas_foro'
        ));
    }

    public function subirEntrega(Request $request)
    {
        $estudiante_id = $this->getUsuarioId($request);
        
        if (!$estudiante_id) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $validated = $request->validate([
            'evaluacion_id' => 'required|integer',
            'archivo' => 'required|file|mimes:pdf',
            'descripcion' => 'nullable|string',
        ]);

        $archivo = $request->file('archivo')->store('uploads/entregas', 'public');

        $yaEntregado = DB::table('entregas')
            ->where('estudiante_id', $estudiante_id)
            ->where('evaluacion_id', $validated['evaluacion_id'])
            ->first();

        if ($yaEntregado) {
            DB::table('entregas')->where('id_entrega', $yaEntregado->id_entrega)->update([
                'archivo' => basename($archivo),
                'descripcion' => $validated['descripcion'],
                'fecha_entrega' => now(),
            ]);
        } else {
            DB::table('entregas')->insert([
                'estudiante_id' => $estudiante_id,
                'evaluacion_id' => $validated['evaluacion_id'],
                'archivo' => basename($archivo),
                'descripcion' => $validated['descripcion'],
                'fecha_entrega' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function responderForo(Request $request)
    {
        $estudiante_id = $this->getUsuarioId($request);
        
        if (!$estudiante_id) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $validated = $request->validate([
            'foro_id' => 'required|integer',
            'contenido' => 'required|string',
            'archivo' => 'nullable|file|mimes:pdf',
        ]);

        $archivo = null;
        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo')->store('uploads/foros', 'public');
        }

        DB::table('respuestas_foro')->insert([
            'foro_id' => $validated['foro_id'],
            'estudiante_id' => $estudiante_id,
            'contenido' => $validated['contenido'],
            'archivo' => $archivo ? basename($archivo) : null,
            'fecha_respuesta' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function calendario(Request $request)
    {
        $usuario_id = $this->getUsuarioId($request);
        
        if (!$usuario_id) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        
        $grupo_id = DB::table('matriculas')->where('estudiante_id', $usuario_id)->orderByDesc('id_matricula')->value('grupo_id');

        $eventos = [];

        if ($grupo_id) {
            $asignaciones = DB::table('asignaciones')
                ->join('cursos', 'asignaciones.curso_id', '=', 'cursos.id_curso')
                ->where('asignaciones.grupo_id', $grupo_id)
                ->select('cursos.nombre as curso', 'asignaciones.dia_semana', 'asignaciones.hora_inicio', 'asignaciones.hora_fin')
                ->get();

            $diaMap = ['Lunes' => 1, 'Martes' => 2, 'Miércoles' => 3, 'Jueves' => 4, 'Viernes' => 5, 'Sábado' => 6, 'Domingo' => 0];

            foreach ($asignaciones as $a) {
                $eventos[] = [
                    'title' => $a->curso,
                    'daysOfWeek' => [$diaMap[$a->dia_semana] ?? 0],
                    'startTime' => substr($a->hora_inicio, 0, 5),
                    'endTime' => substr($a->hora_fin, 0, 5),
                ];
            }
        }

        return response()->json($eventos);
    }

    public function configuracion(Request $request)
    {
        $usuario_id = $this->getUsuarioId($request);
        
        if (!$usuario_id) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        
        $usuario = DB::table('usuarios')->where('id', $usuario_id)->first();

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'nombre' => 'required|string',
                'apellido' => 'required|string',
                'usuario' => 'required|string',
                'email' => 'required|email',
                'genero' => 'required|string',
                'password' => 'nullable|string|min:6',
            ]);

            $updateData = $validated;
            if (!empty($validated['password'])) {
                $updateData['password'] = password_hash($validated['password'], PASSWORD_DEFAULT);
            } else {
                unset($updateData['password']);
            }

            DB::table('usuarios')->where('id', $usuario_id)->update($updateData);
            return response()->json(['success' => true]);
        }

        return response()->json($usuario);
    }

    public function notas(Request $request)
    {
        $usuario_id = $this->getUsuarioId($request);
        
        if (!$usuario_id) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        $matriculas = DB::table('matriculas as m')
            ->join('grupos as g', 'm.grupo_id', '=', 'g.id_grupo')
            ->join('asignaciones as a', 'a.grupo_id', '=', 'g.id_grupo')
            ->join('cursos as c', 'a.curso_id', '=', 'c.id_curso')
            ->where('m.estudiante_id', $usuario_id)
            ->select('c.id_curso', 'c.nombre as curso', 'g.nombre as grupo', 'g.id_grupo')
            ->groupBy('c.id_curso', 'g.id_grupo', 'c.nombre', 'g.nombre')
            ->get();

        $cursosArr = [];

        foreach ($matriculas as $mat) {
            $evaluaciones = DB::table('evaluaciones as e')
                ->leftJoin('notas as n', function($join) use ($usuario_id) {
                    $join->on('n.origen_id', '=', 'e.id_evaluacion')
                        ->where('n.tipo', 'evaluacion')
                        ->where('n.estudiante_id', $usuario_id);
                })
                ->where('e.curso_id', $mat->id_curso)
                ->where('e.grupo_id', $mat->id_grupo)
                ->select('e.titulo', 'e.descripcion', 'n.nota')
                ->orderByDesc('e.fecha')
                ->get();

            $foros = DB::table('foro as f')
                ->leftJoin('notas as n', function($join) use ($usuario_id) {
                    $join->on('n.origen_id', '=', 'f.id_foro')
                        ->where('n.tipo', 'foro')
                        ->where('n.estudiante_id', $usuario_id);
                })
                ->where('f.curso_id', $mat->id_curso)
                ->where('f.grupo_id', $mat->id_grupo)
                ->select('f.titulo', 'f.contenido as descripcion', 'n.nota')
                ->orderByDesc('f.fecha_publicacion')
                ->get();

            $cursosArr[] = [
                'curso' => $mat->curso,
                'grupo' => $mat->grupo,
                'evaluaciones' => $evaluaciones,
                'foros' => $foros,
            ];
        }

        return response()->json(['cursos' => $cursosArr]);
    }
}
