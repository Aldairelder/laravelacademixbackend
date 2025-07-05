<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asignacion;
use App\Models\Usuario;

class ProfesorController extends Controller
{
    public function cursos(Request $request)
    {
        $usuario_id = session('usuario_id');
        $asignaciones = Asignacion::with(['curso', 'grupo'])
            ->where('docente_id', $usuario_id)
            ->get()
            ->unique(function($item) {
                return $item->grupo_id . '-' . $item->curso_id;
            })->values();
        return view('profesor.cursos', compact('asignaciones'));
    }

    public function detalleCurso(Request $request, $grupo_id, $curso_id)
    {
        // Procesar formulario de Materiales
        if (
    $request->isMethod('post') && 
    $request->has('titulo') &&
    !$request->has('guardar_evaluacion') &&
    !$request->has('editar_evaluacion') &&
    !$request->has('guardar_foro') &&
    !$request->has('editar_foro')
) {
    $request->validate([
        'titulo' => 'required|string',
        'descripcion' => 'required|string',
        'archivo' => 'nullable|file|mimes:pdf',
    ]);
    $archivo = null;
    if ($request->hasFile('archivo')) {
        $archivo = $request->file('archivo')->store('uploads/materiales', 'public');
    }
    if ($request->has('edit_material')) {
        $updateData = [
            'titulo' => $request->input('titulo'),
            'descripcion' => $request->input('descripcion'),
            'fecha_subida' => now(),
        ];
        if ($archivo) {
            $updateData['archivo'] = $archivo;
        }
        \DB::table('materiales')->where('id_material', $request->input('edit_material'))
            ->update($updateData);
    } else {
        \DB::table('materiales')->insert([
            'titulo' => $request->input('titulo'),
            'descripcion' => $request->input('descripcion'),
            'archivo' => $archivo,
            'fecha_subida' => now(),
            'grupo_id' => $grupo_id,
            'curso_id' => $curso_id,
        ]);
    }
    return redirect()->route('profesor.detalle_curso', [$grupo_id, $curso_id, 'tab' => 'materiales']);
}

        // Procesar formulario de Evaluaciones
        if ($request->isMethod('post') && ($request->has('guardar_evaluacion') || $request->has('editar_evaluacion'))) {
            $request->validate([
                'titulo' => 'required|string',
                'descripcion' => 'required|string',
                'fecha' => 'required|date',
                'archivo' => 'nullable|file|mimes:pdf',
            ]);
            $archivo = null;
            if ($request->hasFile('archivo')) {
                $archivo = $request->file('archivo')->store('uploads/evaluaciones', 'public');
            }
            if ($request->has('editar_evaluacion') && $request->filled('eval_id')) {
                $updateData = [
                    'titulo' => $request->input('titulo'),
                    'descripcion' => $request->input('descripcion'),
                    'fecha' => $request->input('fecha'),
                ];
                if ($archivo) {
                    $updateData['archivo'] = $archivo;
                }
                $updated = \DB::table('evaluaciones')->where('id_evaluacion', $request->input('eval_id'))
                    ->update($updateData);
                if (!$updated) {
                    return back()->withErrors(['No se pudo actualizar la evaluación.']);
                }
            } else {
                \DB::table('evaluaciones')->insert([
                    'titulo' => $request->input('titulo'),
                    'descripcion' => $request->input('descripcion'),
                    'fecha' => $request->input('fecha'),
                    'archivo' => $archivo,
                    'grupo_id' => $grupo_id,
                    'curso_id' => $curso_id,
                ]);
            }
            return redirect()->route('profesor.detalle_curso', [$grupo_id, $curso_id, 'tab' => 'evaluaciones']);
        }

        // Procesar formulario de Foros
        if ($request->isMethod('post') && ($request->has('guardar_foro') || $request->has('editar_foro'))) {
            $request->validate([
                'titulo' => 'required|string',
                'contenido' => 'required|string',
                'archivo' => 'nullable|file|mimes:pdf',
            ]);
            $archivo = null;
            if ($request->hasFile('archivo')) {
                $archivo = $request->file('archivo')->store('uploads/foros', 'public');
            }
            if ($request->has('editar_foro') && $request->filled('edit_foro')) {
                $updateData = [
                    'titulo' => $request->input('titulo'),
                    'contenido' => $request->input('contenido'),
                ];
                if ($archivo) {
                    $updateData['archivo'] = $archivo;
                }
                $updated = \DB::table('foro')->where('id_foro', $request->input('edit_foro'))
                    ->update($updateData);
                if (!$updated) {
                    return back()->withErrors(['No se pudo actualizar el foro.']);
                }
            } else {
                \DB::table('foro')->insert([
                    'grupo_id' => $grupo_id,
                    'curso_id' => $curso_id,
                    'titulo' => $request->input('titulo'),
                    'contenido' => $request->input('contenido'),
                    'archivo' => $archivo,
                    'fecha_publicacion' => now(),
                ]);
            }
            return redirect()->route('profesor.detalle_curso', [$grupo_id, $curso_id, 'tab' => 'foros']);
        }

        $grupo = \DB::table('grupos')
            ->join('cursos', 'cursos.id_curso', '=', \DB::raw($curso_id))
            ->where('grupos.id_grupo', $grupo_id)
            ->select('grupos.nombre as grupo', 'cursos.nombre as curso')
            ->first();

        $materiales = \DB::table('materiales')
            ->where('grupo_id', $grupo_id)
            ->where('curso_id', $curso_id)
            ->orderByDesc('fecha_subida')
            ->get();

        $evaluaciones = \DB::table('evaluaciones')
            ->where('grupo_id', $grupo_id)
            ->where('curso_id', $curso_id)
            ->orderByDesc('fecha')
            ->get()
            ->map(function($e) {
                $pendientes = \DB::table('entregas')
                    ->where('evaluacion_id', $e->id_evaluacion)
                    ->whereNotIn('estudiante_id', function($q) use ($e) {
                        $q->select('estudiante_id')
                          ->from('notas')
                          ->where('tipo', 'evaluacion')
                          ->where('origen_id', $e->id_evaluacion);
                    })
                    ->count();
                $e->pendientes = $pendientes;
                return $e;
            });

        $foros = \DB::table('foro')
            ->where('grupo_id', $grupo_id)
            ->where('curso_id', $curso_id)
            ->orderByDesc('fecha_publicacion')
            ->get()
            ->map(function($f) {
                $pendientes = \DB::table('respuestas_foro')
                    ->where('foro_id', $f->id_foro)
                    ->whereNotIn('estudiante_id', function($q) use ($f) {
                        $q->select('estudiante_id')
                          ->from('notas')
                          ->where('tipo', 'foro')
                          ->where('origen_id', $f->id_foro);
                    })
                    ->count();
                $f->pendientes = $pendientes;
                return $f;
            });

        $edit_material = null;
        $edit_eval = null;
        $edit_foro = null;
        if (request()->has('edit_material')) {
            $edit_material = \DB::table('materiales')
                ->where('id_material', request('edit_material'))
                ->where('grupo_id', $grupo_id)
                ->where('curso_id', $curso_id)
                ->first();
        }
        if (request()->has('edit_eval')) {
            $edit_eval = \DB::table('evaluaciones')
                ->where('id_evaluacion', request('edit_eval'))
                ->where('grupo_id', $grupo_id)
                ->where('curso_id', $curso_id)
                ->first();
        }
        if (request()->has('edit_foro')) {
            $edit_foro = \DB::table('foro')
                ->where('id_foro', request('edit_foro'))
                ->where('grupo_id', $grupo_id)
                ->where('curso_id', $curso_id)
                ->first();
        }
        return view('profesor.detalle_curso', compact('grupo', 'materiales', 'evaluaciones', 'foros', 'edit_material', 'edit_eval', 'edit_foro', 'grupo_id', 'curso_id'));
    }

    public function verEntregas($evaluacion_id)
    {
        $evaluacion = \DB::table('evaluaciones')->where('id_evaluacion', $evaluacion_id)->first();
        if (!$evaluacion) {
            abort(404, 'Evaluación no encontrada');
        }
        $grupo_id = $evaluacion->grupo_id;
        $curso_id = $evaluacion->curso_id;
        $entregas = \DB::table('entregas')
            ->join('usuarios', 'entregas.estudiante_id', '=', 'usuarios.id')
            ->leftJoin('notas', function($join) use ($evaluacion_id) {
                $join->on('notas.estudiante_id', '=', 'entregas.estudiante_id')
                    ->where('notas.tipo', 'evaluacion')
                    ->where('notas.origen_id', $evaluacion_id);
            })
            ->select('entregas.*', 'usuarios.nombre', 'usuarios.apellido', 'notas.nota')
            ->where('entregas.evaluacion_id', $evaluacion_id)
            ->get();
        return view('profesor.ver_entregas', compact('evaluacion', 'entregas', 'grupo_id', 'curso_id'));
    }

    public function verRespuestas(Request $request, $foro_id)
    {
        $foro = \DB::table('foro')->where('id_foro', $foro_id)->first();
        if (!$foro) {
            abort(404, 'Foro no encontrado');
        }
        $grupo_id = $foro->grupo_id;
        $curso_id = $foro->curso_id;
        $respuestas = \DB::table('respuestas_foro')
            ->join('usuarios', 'respuestas_foro.estudiante_id', '=', 'usuarios.id')
            ->leftJoin('notas', function($join) use ($foro_id) {
                $join->on('notas.estudiante_id', '=', 'respuestas_foro.estudiante_id')
                    ->where('notas.tipo', 'foro')
                    ->where('notas.origen_id', $foro_id);
            })
            ->select('respuestas_foro.*', 'usuarios.nombre', 'usuarios.apellido', 'notas.nota')
            ->where('respuestas_foro.foro_id', $foro_id)
            ->orderByDesc('respuestas_foro.fecha_respuesta')
            ->get();
        return view('profesor.ver_respuestas', compact('foro', 'respuestas', 'grupo_id', 'curso_id'));
    }

    public function guardarNotaEntrega(Request $request, $evaluacion_id)
    {
        $request->validate([
            'estudiante_id' => 'required|integer',
            'nota' => 'required|numeric|min:0|max:20',
        ]);
        $estudiante_id = $request->estudiante_id;
        $nota = $request->nota;
        $existe = \DB::table('notas')
            ->where('estudiante_id', $estudiante_id)
            ->where('tipo', 'evaluacion')
            ->where('origen_id', $evaluacion_id)
            ->first();
        if ($existe) {
            \DB::table('notas')
                ->where('estudiante_id', $estudiante_id)
                ->where('tipo', 'evaluacion')
                ->where('origen_id', $evaluacion_id)
                ->update(['nota' => $nota, 'fecha_registro' => now()]);
        } else {
            \DB::table('notas')->insert([
                'estudiante_id' => $estudiante_id,
                'tipo' => 'evaluacion',
                'origen_id' => $evaluacion_id,
                'nota' => $nota,
                'fecha_registro' => now(),
            ]);
        }
        return redirect()->back();
    }

    public function guardarNotaRespuesta(Request $request, $foro_id)
    {
        $request->validate([
            'estudiante_id' => 'required|integer',
            'nota' => 'required|numeric|min:0|max:20',
        ]);
        $estudiante_id = $request->estudiante_id;
        $nota = $request->nota;
        $existe = \DB::table('notas')
            ->where('estudiante_id', $estudiante_id)
            ->where('tipo', 'foro')
            ->where('origen_id', $foro_id)
            ->first();
        if ($existe) {
            \DB::table('notas')
                ->where('estudiante_id', $estudiante_id)
                ->where('tipo', 'foro')
                ->where('origen_id', $foro_id)
                ->update(['nota' => $nota, 'fecha_registro' => now()]);
        } else {
            \DB::table('notas')->insert([
                'estudiante_id' => $estudiante_id,
                'tipo' => 'foro',
                'origen_id' => $foro_id,
                'nota' => $nota,
                'fecha_registro' => now(),
            ]);
        }
        return redirect()->back();
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
            return redirect()->route('profesor.configuracion')->with('success', 'Datos actualizados correctamente.');
        }
        return view('profesor.configuracion', compact('usuario'));
    }

    public function asistencia(Request $request, $grupo_id = null, $curso_id = null)
    {
        $grupos = \DB::table('grupos')->get();
        $cursos = collect();
        $alumnos = collect();
        $asistencias_por_fecha = [];
        if ($grupo_id) {
            $cursos = \DB::table('asignaciones')
                ->join('cursos', 'asignaciones.curso_id', '=', 'cursos.id_curso')
                ->where('asignaciones.grupo_id', $grupo_id)
                ->select('cursos.id_curso', 'cursos.nombre')
                ->distinct()->get();
            $alumnos = \DB::table('matriculas')
                ->join('usuarios', 'matriculas.estudiante_id', '=', 'usuarios.id')
                ->where('matriculas.grupo_id', $grupo_id)
                ->select('usuarios.id', 'usuarios.nombre', 'usuarios.apellido')
                ->get();
        }
        if ($request->isMethod('post') && $request->has('guardar_asistencia')) {
            $fecha = $request->input('fecha_asistencia');
            $asistencias = $request->input('asistencia', []);
            $observaciones = $request->input('observacion', []);
            foreach ($asistencias as $estudiante_id => $estado) {
                $observacion = $observaciones[$estudiante_id] ?? null;
                \DB::table('asistencia')->updateOrInsert(
                    [
                        'grupo_id' => $grupo_id,
                        'curso_id' => $curso_id,
                        'estudiante_id' => $estudiante_id,
                        'fecha' => $fecha,
                    ],
                    [
                        'estado' => $estado,
                        'observacion' => $observacion,
                    ]
                );
            }
            return redirect()->route('profesor.asistencia', [$grupo_id, $curso_id])->with('success', 'Asistencia registrada/actualizada correctamente.');
        }
        if ($grupo_id && $curso_id) {
            $fechas = \DB::table('asistencia')
                ->where('grupo_id', $grupo_id)
                ->where('curso_id', $curso_id)
                ->select('fecha')
                ->distinct()
                ->orderByDesc('fecha')
                ->get();
            foreach ($fechas as $f) {
                $lista = \DB::table('asistencia')
                    ->join('usuarios', 'asistencia.estudiante_id', '=', 'usuarios.id')
                    ->where('asistencia.grupo_id', $grupo_id)
                    ->where('asistencia.curso_id', $curso_id)
                    ->where('asistencia.fecha', $f->fecha)
                    ->select('asistencia.*', 'usuarios.nombre', 'usuarios.apellido')
                    ->get();
                $asistencias_por_fecha[$f->fecha] = $lista;
            }
        }
        return view('profesor.asistencia', compact('grupos', 'cursos', 'alumnos', 'asistencias_por_fecha', 'grupo_id', 'curso_id'));
    }
}
