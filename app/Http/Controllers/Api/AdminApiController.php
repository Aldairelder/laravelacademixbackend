<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminApiController extends Controller
{
    // Usuarios
    public function usuariosIndex(Request $request)
{
    $query = DB::table('usuarios')
        ->leftJoin('roles', 'usuarios.rol_id', '=', 'roles.id')
        ->select('usuarios.*', 'roles.nombre as rol_nombre');

    // Filtro por nombre de rol, si viene en el query string
    if ($request->has('rol')) {
        $query->where('roles.nombre', $request->rol);
    }

    $usuarios = $query->get()->map(function ($u) {
        $u->rol = (object)['nombre' => $u->rol_nombre];
        return $u;
    });

    return response()->json($usuarios);
}


    public function storeUsuario(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'usuario' => 'required|string|max:100|unique:usuarios,usuario',
            'email' => 'required|email|max:100|unique:usuarios,email',
            'password' => 'required|string|min:6',
            'rol_id' => 'required|integer|exists:roles,id',
            'genero' => 'required|string|max:10',
        ]);

        $id = DB::table('usuarios')->insertGetId([
            'nombre' => $validated['nombre'],
            'apellido' => $validated['apellido'],
            'usuario' => $validated['usuario'],
            'email' => $validated['email'],
            'password' => password_hash($validated['password'], PASSWORD_DEFAULT),
            'rol_id' => $validated['rol_id'],
            'genero' => $validated['genero'],
        ]);

        return response()->json(['message' => 'Usuario creado correctamente', 'usuario_id' => $id], 201);
    }

    public function showUsuario($id)
    {
        $u = DB::table('usuarios')
            ->leftJoin('roles', 'usuarios.rol_id', '=', 'roles.id')
            ->select('usuarios.*', 'roles.nombre as rol_nombre')
            ->where('usuarios.id', $id)
            ->first();

        if (!$u) return response()->json(['error' => 'Usuario no encontrado'], 404);

        $u->rol = (object)['nombre' => $u->rol_nombre];
        return response()->json($u);
    }

    public function updateUsuario(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'required|string',
            'apellido' => 'required|string',
            'usuario' => 'required|string|unique:usuarios,usuario,'.$id,
            'email' => 'required|email|unique:usuarios,email,'.$id,
            'rol_id' => 'required|integer|exists:roles,id',
            'genero' => 'required|string',
            'password' => 'nullable|string|min:6',
        ]);

        $updateData = [
            'nombre' => $validated['nombre'],
            'apellido' => $validated['apellido'],
            'usuario' => $validated['usuario'],
            'email' => $validated['email'],
            'rol_id' => $validated['rol_id'],
            'genero' => $validated['genero'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = password_hash($validated['password'], PASSWORD_DEFAULT);
        }

        $updated = DB::table('usuarios')->where('id', $id)->update($updateData);
        if (!$updated) return response()->json(['error' => 'Usuario no encontrado o sin cambios'], 404);

        return response()->json(['message' => 'Usuario actualizado correctamente']);
    }

    public function destroyUsuario($id)
    {
        try {
            $deleted = DB::table('usuarios')->where('id', $id)->delete();
            if (!$deleted) return response()->json(['error' => 'Usuario no encontrado'], 404);
            return response()->json(['message' => 'Usuario eliminado correctamente']);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return response()->json(['error' => 'No se puede eliminar el usuario porque tiene registros relacionados.'], 400);
            }
            return response()->json(['error' => 'Error al eliminar el usuario.'], 500);
        }
    }

    // Roles
    public function rolesIndex()
    {
        return response()->json(DB::table('roles')->get());
    }

    public function storeRol(Request $request)
    {
        $validated = $request->validate(['nombre' => 'required|string|max:50']);
        $id = DB::table('roles')->insertGetId(['nombre' => $validated['nombre']]);
        return response()->json(['message' => 'Rol creado correctamente', 'rol_id' => $id], 201);
    }

    public function showRol($id)
    {
        $rol = DB::table('roles')->where('id', $id)->first();
        if (!$rol) return response()->json(['error' => 'Rol no encontrado'], 404);
        return response()->json($rol);
    }

    public function updateRol(Request $request, $id)
    {
        $validated = $request->validate(['nombre' => 'required|string|max:50']);
        $updated = DB::table('roles')->where('id', $id)->update(['nombre' => $validated['nombre']]);
        if (!$updated) return response()->json(['error' => 'Rol no encontrado o sin cambios'], 404);
        return response()->json(['message' => 'Rol actualizado correctamente']);
    }

    public function destroyRol($id)
    {
        try {
            $deleted = DB::table('roles')->where('id', $id)->delete();
            if (!$deleted) return response()->json(['error' => 'Rol no encontrado'], 404);
            return response()->json(['message' => 'Rol eliminado correctamente']);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return response()->json(['error' => 'No se puede eliminar el rol porque tiene usuarios asignados.'], 400);
            }
            return response()->json(['error' => 'Error al eliminar el rol.'], 500);
        }
    }

    // Cursos
    public function cursosIndex()
{
    $cursos = DB::table('cursos')
        ->leftJoin('asignaciones', 'cursos.id_curso', '=', 'asignaciones.curso_id')
        ->leftJoin('usuarios', 'asignaciones.docente_id', '=', 'usuarios.id')
        ->leftJoin('matriculas', 'asignaciones.grupo_id', '=', 'matriculas.grupo_id')
        ->select(
            'cursos.id_curso',
            'cursos.nombre',
            'cursos.descripcion',
            'cursos.grado',
            DB::raw('CONCAT(usuarios.nombre, " ", usuarios.apellido) as profesor'),
            DB::raw('COUNT(matriculas.id_matricula) as total_estudiantes')
        )
        ->groupBy('cursos.id_curso', 'cursos.nombre', 'cursos.descripcion', 'cursos.grado', 'usuarios.nombre', 'usuarios.apellido')
        ->get();

    return response()->json($cursos);
}



    public function storeCurso(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'grado' => 'nullable|string|max:50',
        ]);
        $id = DB::table('cursos')->insertGetId($validated);
        return response()->json(['message' => 'Curso registrado correctamente', 'curso_id' => $id], 201);
    }

    public function showCurso($id)
    {
        $curso = DB::table('cursos')->where('id_curso', $id)->first();
        return $curso ? response()->json($curso) : response()->json(['error' => 'Curso no encontrado'], 404);
    }

    public function updateCurso(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'grado' => 'nullable|string|max:50',
        ]);
        $updated = DB::table('cursos')->where('id_curso', $id)->update($validated);
        if (!$updated) return response()->json(['error' => 'Curso no encontrado o sin cambios'], 404);
        return response()->json(['message' => 'Curso actualizado correctamente']);
    }

    public function destroyCurso($id)
    {
        try {
            $deleted = DB::table('cursos')->where('id_curso', $id)->delete();
            if (!$deleted) return response()->json(['error' => 'Curso no encontrado'], 404);
            return response()->json(['message' => 'Curso eliminado correctamente']);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return response()->json(['error' => 'No se puede eliminar el curso porque está asignado o tiene registros relacionados.'], 400);
            }
            return response()->json(['error' => 'Error al eliminar el curso.'], 500);
        }
    }

    // Grupos
    public function gruposIndex()
    {
        return response()->json(DB::table('grupos')->get());
    }

    public function storeGrupo(Request $request)
    {
        $validated = $request->validate(['nombre' => 'required|string|max:100']);
        $id = DB::table('grupos')->insertGetId(['nombre' => $validated['nombre']]);
        return response()->json(['message' => 'Grupo creado correctamente', 'grupo_id' => $id], 201);
    }

    public function showGrupo($id)
    {
        $grupo = DB::table('grupos')->where('id_grupo', $id)->first();
        return $grupo ? response()->json($grupo) : response()->json(['error' => 'Grupo no encontrado'], 404);
    }

    public function updateGrupo(Request $request, $id)
    {
        $validated = $request->validate(['nombre' => 'required|string|max:100']);
        $updated = DB::table('grupos')->where('id_grupo', $id)->update(['nombre' => $validated['nombre']]);
        if (!$updated) return response()->json(['error' => 'Grupo no encontrado o sin cambios'], 404);
        return response()->json(['message' => 'Grupo actualizado correctamente']);
    }

    public function destroyGrupo($id)
    {
        try {
            $deleted = DB::table('grupos')->where('id_grupo', $id)->delete();
            if (!$deleted) return response()->json(['error' => 'Grupo no encontrado'], 404);
            return response()->json(['message' => 'Grupo eliminado correctamente']);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return response()->json(['error' => 'No se puede eliminar el grupo porque tiene registros relacionados.'], 400);
            }
            return response()->json(['error' => 'Error al eliminar el grupo.'], 500);
        }
    }

    // Matrículas
    public function matriculasIndex()
    {
        $matriculas = DB::table('matriculas')
            ->join('usuarios', 'matriculas.estudiante_id', '=', 'usuarios.id')
            ->join('grupos', 'matriculas.grupo_id', '=', 'grupos.id_grupo')
            ->select('matriculas.*', 'usuarios.nombre', 'usuarios.apellido', 'grupos.nombre as grupo')
            ->get();

        return response()->json($matriculas);
    }

    public function storeMatricula(Request $request)
    {
        $validated = $request->validate([
            'estudiante_id' => 'required|exists:usuarios,id',
            'grupo_id' => 'required|exists:grupos,id_grupo',
            'fecha' => 'required|date',
        ]);

        $id = DB::table('matriculas')->insertGetId($validated);
        return response()->json(['message' => 'Matrícula registrada correctamente', 'matricula_id' => $id], 201);
    }

    public function showMatricula($id)
    {
        $m = DB::table('matriculas')->where('id_matricula', $id)->first();
        return $m ? response()->json($m) : response()->json(['error' => 'Matrícula no encontrada'], 404);
    }

    public function updateMatricula(Request $request, $id)
    {
        $validated = $request->validate([
            'estudiante_id' => 'required|exists:usuarios,id',
            'grupo_id' => 'required|exists:grupos,id_grupo',
            'fecha' => 'required|date',
        ]);

        $updated = DB::table('matriculas')->where('id_matricula', $id)->update($validated);
        if (!$updated) return response()->json(['error' => 'Matrícula no encontrada o sin cambios'], 404);
        return response()->json(['message' => 'Matrícula actualizada correctamente']);
    }

    public function destroyMatricula($id)
    {
        $deleted = DB::table('matriculas')->where('id_matricula', $id)->delete();
        if (!$deleted) return response()->json(['error' => 'Matrícula no encontrada'], 404);
        return response()->json(['message' => 'Matrícula eliminada correctamente']);
    }

    // Asignaciones
    public function asignacionesIndex()
    {
        $asign = DB::table('asignaciones')
            ->join('grupos', 'asignaciones.grupo_id', '=', 'grupos.id_grupo')
            ->join('cursos', 'asignaciones.curso_id', '=', 'cursos.id_curso')
            ->join('usuarios', 'asignaciones.docente_id', '=', 'usuarios.id')
            ->select('asignaciones.*', 'grupos.nombre as grupo', 'cursos.nombre as curso', 'usuarios.nombre as docente')
            ->get();
        return response()->json($asign);
    }

    public function storeAsignacion(Request $request)
    {
        $validated = $request->validate([
            'grupo_id' => 'required|exists:grupos,id_grupo',
            'curso_id' => 'required|exists:cursos,id_curso',
            'docente_id' => 'required|exists:usuarios,id',
            'dia_semana' => 'required|string',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
        ]);

        $id = DB::table('asignaciones')->insertGetId($validated);
        return response()->json(['message' => 'Asignación creada correctamente', 'asignacion_id' => $id], 201);
    }

    public function showAsignacion($id)
    {
        $a = DB::table('asignaciones')->where('id_asignacion', $id)->first();
        return $a ? response()->json($a) : response()->json(['error' => 'Asignación no encontrada'], 404);
    }

    public function updateAsignacion(Request $request, $id)
    {
        $validated = $request->validate([
            'grupo_id' => 'required|exists:grupos,id_grupo',
            'curso_id' => 'required|exists:cursos,id_curso',
            'docente_id' => 'required|exists:usuarios,id',
            'dia_semana' => 'required|string',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
        ]);

        $updated = DB::table('asignaciones')->where('id_asignacion', $id)->update($validated);
        if (!$updated) return response()->json(['error' => 'Asignación no encontrada o sin cambios'], 404);
        return response()->json(['message' => 'Asignación actualizada correctamente']);
    }

    public function destroyAsignacion($id)
    {
        $deleted = DB::table('asignaciones')->where('id_asignacion', $id)->delete();
        if (!$deleted) return response()->json(['error' => 'Asignación no encontrada'], 404);
        return response()->json(['message' => 'Asignación eliminada correctamente']);
    }
}
