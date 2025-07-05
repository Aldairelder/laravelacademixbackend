<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EstudianteController extends Controller
{
    public function panelEstudiante()
    {
        return view('estudiante.panel');
    }
    public function cursosEstudiante(Request $request)
    {
        $estudiante_id = session('usuario_id');
        $cursos = \DB::table('matriculas as m')
            ->join('grupos as g', 'm.grupo_id', '=', 'g.id_grupo')
            ->join('asignaciones as a', 'a.grupo_id', '=', 'g.id_grupo')
            ->join('cursos as c', 'a.curso_id', '=', 'c.id_curso')
            ->where('m.estudiante_id', $estudiante_id)
            ->select('c.id_curso', 'c.nombre as curso', 'g.id_grupo', 'g.nombre as grupo')
            ->distinct()
            ->get();
        return view('estudiante.cursos', compact('cursos'));
    }
    public function detalleCurso(Request $request, $grupo_id, $curso_id)
    {
        $estudiante_id = session('usuario_id');
        // Validar matrícula
        $matricula = \DB::table('matriculas')
            ->where('estudiante_id', $estudiante_id)
            ->where('grupo_id', $grupo_id)
            ->first();
        if (!$matricula) {
            abort(403, 'Acceso no autorizado');
        }

        // Subir entrega de evaluación
        if ($request->isMethod('post') && $request->has('evaluacion_id')) {
            $request->validate([
                'archivo' => 'required|file|mimes:pdf',
                'descripcion' => 'nullable|string',
            ]);
            $archivo = $request->file('archivo')->store('uploads/entregas', 'public');
            $evaluacion_id = $request->input('evaluacion_id');
            $descripcion = $request->input('descripcion');
            $yaEntregado = \DB::table('entregas')
                ->where('estudiante_id', $estudiante_id)
                ->where('evaluacion_id', $evaluacion_id)
                ->first();
            if ($yaEntregado) {
                \DB::table('entregas')->where('id_entrega', $yaEntregado->id_entrega)
                    ->update([
                        'archivo' => basename($archivo),
                        'descripcion' => $descripcion,
                        'fecha_entrega' => now(),
                    ]);
            } else {
                \DB::table('entregas')->insert([
                    'estudiante_id' => $estudiante_id,
                    'evaluacion_id' => $evaluacion_id,
                    'archivo' => basename($archivo),
                    'descripcion' => $descripcion,
                    'fecha_entrega' => now(),
                ]);
            }
            return redirect()->route('estudiante.detalle_curso', [$grupo_id, $curso_id, '#evaluaciones']);
        }

        // Responder foro
        if ($request->isMethod('post') && $request->has('foro_id')) {
            $request->validate([
                'contenido' => 'required|string',
                'archivo' => 'nullable|file|mimes:pdf',
            ]);
            $archivo = null;
            if ($request->hasFile('archivo')) {
                $archivo = $request->file('archivo')->store('uploads/foros', 'public');
            }
            \DB::table('respuestas_foro')->insert([
                'foro_id' => $request->input('foro_id'),
                'estudiante_id' => $estudiante_id,
                'contenido' => $request->input('contenido'),
                'archivo' => $archivo ? basename($archivo) : null,
                'fecha_respuesta' => now(),
            ]);
            return redirect()->route('estudiante.detalle_curso', [$grupo_id, $curso_id, '#foros']);
        }

        // Eliminar entrega de evaluación
        if ($request->has('eliminar_entrega')) {
            $id_entrega = $request->input('eliminar_entrega');
            $entrega = \DB::table('entregas')->where('id_entrega', $id_entrega)->where('estudiante_id', $estudiante_id)->first();
            if ($entrega) {
                if ($entrega->archivo && file_exists(public_path('uploads/entregas/' . $entrega->archivo))) {
                    @unlink(public_path('uploads/entregas/' . $entrega->archivo));
                }
                \DB::table('entregas')->where('id_entrega', $id_entrega)->delete();
            }
            return redirect()->route('estudiante.detalle_curso', [$grupo_id, $curso_id, '#evaluaciones']);
        }

        // Eliminar respuesta en foro
        if ($request->has('eliminar_respuesta')) {
            $id_respuesta = $request->input('eliminar_respuesta');
            $respuesta = \DB::table('respuestas_foro')->where('id_respuesta', $id_respuesta)->where('estudiante_id', $estudiante_id)->first();
            if ($respuesta) {
                if ($respuesta->archivo && file_exists(public_path('uploads/foros/' . $respuesta->archivo))) {
                    @unlink(public_path('uploads/foros/' . $respuesta->archivo));
                }
                \DB::table('respuestas_foro')->where('id_respuesta', $id_respuesta)->delete();
            }
            return redirect()->route('estudiante.detalle_curso', [$grupo_id, $curso_id, '#foros']);
        }

        $grupo = \DB::table('grupos')
            ->join('asignaciones', 'asignaciones.grupo_id', '=', 'grupos.id_grupo')
            ->join('cursos', 'asignaciones.curso_id', '=', 'cursos.id_curso')
            ->where('grupos.id_grupo', $grupo_id)
            ->where('cursos.id_curso', $curso_id)
            ->select('grupos.nombre as grupo', 'cursos.nombre as curso')
            ->first();
        $materiales = \DB::table('materiales')
            ->where('grupo_id', $grupo_id)
            ->where('curso_id', $curso_id)
            ->orderBy('fecha_subida', 'desc')
            ->get();
        $evaluaciones = \DB::table('evaluaciones')
            ->where('grupo_id', $grupo_id)
            ->where('curso_id', $curso_id)
            ->orderBy('fecha', 'desc')
            ->get();
        $foros = \DB::table('foro')
            ->where('grupo_id', $grupo_id)
            ->where('curso_id', $curso_id)
            ->orderBy('fecha_publicacion', 'desc')
            ->get();
        $notas = \DB::table('notas')
            ->where('estudiante_id', $estudiante_id)
            ->get()->keyBy(function($n){ return $n->tipo.'_'.$n->origen_id; });
        $entregas = \DB::table('entregas')
            ->where('estudiante_id', $estudiante_id)
            ->get()->keyBy('evaluacion_id');
        $respuestas_foro = \DB::table('respuestas_foro')
            ->join('usuarios', 'usuarios.id', '=', 'respuestas_foro.estudiante_id')
            ->select('respuestas_foro.*', 'usuarios.nombre', 'usuarios.apellido')
            ->get();
        return view('estudiante.detalle_curso', compact('grupo', 'materiales', 'evaluaciones', 'foros', 'notas', 'entregas', 'respuestas_foro', 'grupo_id', 'curso_id'));
    }
    public function calendario(Request $request)
    {
        $usuario_id = session('usuario_id');
        $grupo_id = \DB::table('matriculas')->where('estudiante_id', $usuario_id)->orderByDesc('id_matricula')->value('grupo_id');
        $eventos = [];
        if ($grupo_id) {
            $asignaciones = \DB::table('asignaciones')
                ->join('cursos', 'asignaciones.curso_id', '=', 'cursos.id_curso')
                ->where('asignaciones.grupo_id', $grupo_id)
                ->select('cursos.nombre as curso', 'asignaciones.dia_semana', 'asignaciones.hora_inicio', 'asignaciones.hora_fin')
                ->get();
            $diaMap = [
                'Lunes' => 1,
                'Martes' => 2,
                'Miércoles' => 3,
                'Jueves' => 4,
                'Viernes' => 5,
                'Sábado' => 6,
                'Domingo' => 0
            ];
            foreach ($asignaciones as $a) {
                $eventos[] = [
                    'title' => $a->curso,
                    'daysOfWeek' => [$diaMap[$a->dia_semana] ?? 0],
                    'startTime' => substr($a->hora_inicio, 0, 5),
                    'endTime' => substr($a->hora_fin, 0, 5)
                ];
            }
        }
        return view('estudiante.calendario', compact('eventos'));
    }
    public function configuracion(Request $request)
    {
        $usuario_id = session('usuario_id');
        $usuario = \DB::table('usuarios')->where('id', $usuario_id)->first();
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'nombre' => 'required|string',
                'apellido' => 'required|string',
                'usuario' => 'required|string',
                'email' => 'required|email',
                'genero' => 'required|string',
                'password' => 'nullable|string|min:6',
            ]);
            $updateData = [
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
                'usuario' => $validated['usuario'],
                'email' => $validated['email'],
                'genero' => $validated['genero'],
            ];
            if (!empty($validated['password'])) {
                $updateData['password'] = password_hash($validated['password'], PASSWORD_DEFAULT);
            }
            \DB::table('usuarios')->where('id', $usuario_id)->update($updateData);
            return redirect()->route('estudiante.configuracion')->with('success', 'Datos actualizados correctamente.');
        }
        return view('estudiante.configuracion', compact('usuario'));
    }
    public function notas(Request $request)
    {
        $usuario_id = session('usuario_id');
        $matriculas = \DB::table('matriculas as m')
            ->join('grupos as g', 'm.grupo_id', '=', 'g.id_grupo')
            ->join('asignaciones as a', 'a.grupo_id', '=', 'g.id_grupo')
            ->join('cursos as c', 'a.curso_id', '=', 'c.id_curso')
            ->where('m.estudiante_id', $usuario_id)
            ->select('c.id_curso', 'c.nombre as curso', 'g.nombre as grupo', 'g.id_grupo')
            ->groupBy('c.id_curso', 'g.id_grupo', 'c.nombre', 'g.nombre')
            ->get();
        $cursosArr = [];
        foreach ($matriculas as $mat) {
            $evaluaciones = \DB::table('evaluaciones as e')
                ->leftJoin('notas as n', function($join) use ($usuario_id) {
                    $join->on('n.origen_id', '=', 'e.id_evaluacion')
                        ->where('n.tipo', 'evaluacion')
                        ->where('n.estudiante_id', $usuario_id);
                })
                ->where('e.curso_id', $mat->id_curso)
                ->where('e.grupo_id', $mat->id_grupo)
                ->select('e.titulo', 'e.descripcion', 'n.nota')
                ->orderByDesc('e.fecha')
                ->get()
                ->map(function($ev) {
                    return [
                        'titulo' => $ev->titulo,
                        'descripcion' => $ev->descripcion,
                        'nota' => $ev->nota
                    ];
                })->toArray();
            $foros = \DB::table('foro as f')
                ->leftJoin('notas as n', function($join) use ($usuario_id) {
                    $join->on('n.origen_id', '=', 'f.id_foro')
                        ->where('n.tipo', 'foro')
                        ->where('n.estudiante_id', $usuario_id);
                })
                ->where('f.curso_id', $mat->id_curso)
                ->where('f.grupo_id', $mat->id_grupo)
                ->select('f.titulo', 'f.contenido as descripcion', 'n.nota')
                ->orderByDesc('f.fecha_publicacion')
                ->get()
                ->map(function($foro) {
                    return [
                        'titulo' => $foro->titulo,
                        'descripcion' => $foro->descripcion,
                        'nota' => $foro->nota
                    ];
                })->toArray();
            $cursosArr[] = [
                'curso' => $mat->curso,
                'grupo' => $mat->grupo,
                'evaluaciones' => $evaluaciones,
                'foros' => $foros
            ];
        }
        return view('estudiante.notas', ['cursos' => $cursosArr]);
    }
}
