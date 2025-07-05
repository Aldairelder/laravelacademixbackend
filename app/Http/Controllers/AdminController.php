<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function usuariosIndex()
    {
        $usuarios = \DB::table('usuarios')
            ->leftJoin('roles', 'usuarios.rol_id', '=', 'roles.id')
            ->select('usuarios.*', 'roles.nombre as rol_nombre')
            ->get();
        // Si usas Eloquent y modelo Usuario, puedes usar Usuario::with('rol')->get();
        foreach ($usuarios as $u) {
            $u->rol = (object)['nombre' => $u->rol_nombre];
        }
        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function createUsuario()
    {
        $roles = \DB::table('roles')->get();
        return view('admin.usuarios.create', compact('roles'));
    }
    
    public function editUsuario($id)
    {
        $usuario = \DB::table('usuarios')->where('id', $id)->first();
        $roles = \DB::table('roles')->get();
        return view('admin.usuarios.edit', compact('usuario', 'roles'));
    }
    
    public function destroyUsuario($id)
    {
        try {
            \DB::table('usuarios')->where('id', $id)->delete();
            return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado correctamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return redirect()->route('admin.usuarios.index')->with('error', 'No se puede eliminar el usuario porque ya está matriculado o tiene registros relacionados.');
            }
            throw $e;
        }
    }
    
    public function matriculasIndex()
    {
        $matriculas = \DB::table('matriculas')
            ->join('usuarios', 'matriculas.estudiante_id', '=', 'usuarios.id')
            ->join('grupos', 'matriculas.grupo_id', '=', 'grupos.id_grupo')
            ->select('matriculas.*', 'usuarios.nombre', 'usuarios.apellido', 'grupos.nombre as grupo')
            ->get();
        return view('admin.matriculas.index', compact('matriculas'));
    }
    
    public function createMatricula()
    {
        $estudiantes = \DB::table('usuarios')
            ->where('rol_id', 3)
            ->whereNotIn('id', function($q) {
                $q->select('estudiante_id')->from('matriculas');
            })
            ->get();
        $grupos = \DB::table('grupos')->get();
        return view('admin.matriculas.create', compact('estudiantes', 'grupos'));
    }
    
    public function storeMatricula(Request $request)
    {
        $request->validate([
            'estudiante_id' => 'required|exists:usuarios,id',
            'grupo_id' => 'required|exists:grupos,id_grupo',
            'fecha' => 'required|date',
        ]);
        \DB::table('matriculas')->insert([
            'estudiante_id' => $request->estudiante_id,
            'grupo_id' => $request->grupo_id,
            'fecha' => $request->fecha,
        ]);
        return redirect()->route('admin.matriculas.index')->with('success', 'Matrícula registrada correctamente.');
    }
    
    public function destroyMatricula($id)
    {
        \DB::table('matriculas')->where('id_matricula', $id)->delete();
        return redirect()->route('admin.matriculas.index')->with('success', 'Matrícula eliminada correctamente.');
    }
    
    public function editMatricula($id)
    {
        $matricula = \DB::table('matriculas')->where('id_matricula', $id)->first();
        $estudiantes = \DB::table('usuarios')->where('rol_id', 3)->get();
        $grupos = \DB::table('grupos')->get();
        return view('admin.matriculas.edit', compact('matricula', 'estudiantes', 'grupos'));
    }
    
    public function updateMatricula(Request $request, $id)
    {
        $request->validate([
            'estudiante_id' => 'required|exists:usuarios,id',
            'grupo_id' => 'required|exists:grupos,id_grupo',
            'fecha' => 'required|date',
        ]);
        \DB::table('matriculas')->where('id_matricula', $id)->update([
            'estudiante_id' => $request->estudiante_id,
            'grupo_id' => $request->grupo_id,
            'fecha' => $request->fecha,
        ]);
        return redirect()->route('admin.matriculas.index')->with('success', 'Matrícula actualizada correctamente.');
    }
    
    public function cursosIndex()
    {
        $cursos = \DB::table('cursos')->get();
        return view('admin.cursos.index', compact('cursos'));
    }
    
    public function createCurso()
    {
        return view('admin.cursos.create');
    }
    
    public function storeCurso(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'grado' => 'nullable|string|max:50',
        ]);
        \DB::table('cursos')->insert([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'grado' => $request->grado,
        ]);
        return redirect()->route('admin.cursos.index')->with('success', 'Curso registrado correctamente.');
    }
    
    public function editCurso($id)
    {
        $curso = \DB::table('cursos')->where('id_curso', $id)->first();
        return view('admin.cursos.edit', compact('curso'));
    }
    
    public function updateCurso(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'grado' => 'nullable|string|max:50',
        ]);
        \DB::table('cursos')->where('id_curso', $id)->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'grado' => $request->grado,
        ]);
        return redirect()->route('admin.cursos.index')->with('success', 'Curso actualizado correctamente.');
    }
    
    public function destroyCurso($id)
    {
        try {
            \DB::table('cursos')->where('id_curso', $id)->delete();
            return redirect()->route('admin.cursos.index')->with('success', 'Curso eliminado correctamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return redirect()->route('admin.cursos.index')->with('error', 'No se puede eliminar el curso porque está asignado a un grupo o tiene registros relacionados.');
            }
            throw $e;
        }
    }
    
    public function rolesIndex()
    {
        $roles = \DB::table('roles')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function createRol()
    {
        return view('admin.roles.create');
    }

    public function storeRol(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
        ]);
        \DB::table('roles')->insert([
            'nombre' => $request->nombre,
        ]);
        return redirect()->route('admin.roles.index')->with('success', 'Rol creado correctamente.');
    }

    public function editRol($id)
    {
        $rol = \DB::table('roles')->where('id', $id)->first();
        return view('admin.roles.edit', compact('rol'));
    }

    public function updateRol(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
        ]);
        \DB::table('roles')->where('id', $id)->update([
            'nombre' => $request->nombre,
        ]);
        return redirect()->route('admin.roles.index')->with('success', 'Rol actualizado correctamente.');
    }

    public function destroyRol($id)
    {
        try {
            \DB::table('roles')->where('id', $id)->delete();
            return redirect()->route('admin.roles.index')->with('success', 'Rol eliminado correctamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return redirect()->route('admin.roles.index')->with('error', 'No se puede eliminar el rol porque tiene usuarios asignados.');
            }
            throw $e;
        }
    }
    
    public function gruposIndex()
    {
        $grupos = \DB::table('grupos')->get();
        return view('admin.grupos.index', compact('grupos'));
    }

    public function createGrupo()
    {
        return view('admin.grupos.create');
    }

    public function storeGrupo(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);
        \DB::table('grupos')->insert([
            'nombre' => $request->nombre,
        ]);
        return redirect()->route('admin.grupos.index')->with('success', 'Grupo creado correctamente.');
    }

    public function editGrupo($id)
    {
        $grupo = \DB::table('grupos')->where('id_grupo', $id)->first();
        return view('admin.grupos.edit', compact('grupo'));
    }

    public function updateGrupo(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);
        \DB::table('grupos')->where('id_grupo', $id)->update([
            'nombre' => $request->nombre,
        ]);
        return redirect()->route('admin.grupos.index')->with('success', 'Grupo actualizado correctamente.');
    }

    public function destroyGrupo($id)
    {
        try {
            \DB::table('grupos')->where('id_grupo', $id)->delete();
            return redirect()->route('admin.grupos.index')->with('success', 'Grupo eliminado correctamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return redirect()->route('admin.grupos.index')->with('error', 'No se puede eliminar el grupo porque tiene registros relacionados.');
            }
            throw $e;
        }
    }
    
    public function asignacionesIndex()
    {
        $asignaciones = \DB::table('asignaciones')
            ->join('grupos', 'asignaciones.grupo_id', '=', 'grupos.id_grupo')
            ->join('cursos', 'asignaciones.curso_id', '=', 'cursos.id_curso')
            ->join('usuarios', 'asignaciones.docente_id', '=', 'usuarios.id')
            ->select('asignaciones.*', 'grupos.nombre as grupo', 'cursos.nombre as curso', 'usuarios.nombre as docente')
            ->get();
        return view('admin.asignaciones.index', compact('asignaciones'));
    }

    public function createAsignacion()
    {
        $grupos = \DB::table('grupos')->get();
        $cursos = \DB::table('cursos')->get();
        $docentes = \DB::table('usuarios')->where('rol_id', 2)->get();
        return view('admin.asignaciones.create', compact('grupos', 'cursos', 'docentes'));
    }

    public function storeAsignacion(Request $request)
    {
        $request->validate([
            'grupo_id' => 'required|exists:grupos,id_grupo',
            'curso_id' => 'required|exists:cursos,id_curso',
            'docente_id' => 'required|exists:usuarios,id',
            'dia_semana' => 'required',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
        ]);
        \DB::table('asignaciones')->insert([
            'grupo_id' => $request->grupo_id,
            'curso_id' => $request->curso_id,
            'docente_id' => $request->docente_id,
            'dia_semana' => $request->dia_semana,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
        ]);
        return redirect()->route('admin.asignaciones.index')->with('success', 'Asignación creada correctamente.');
    }

    public function editAsignacion($id)
    {
        $asignacion = \DB::table('asignaciones')->where('id_asignacion', $id)->first();
        $grupos = \DB::table('grupos')->get();
        $cursos = \DB::table('cursos')->get();
        $docentes = \DB::table('usuarios')->where('rol_id', 2)->get();
        return view('admin.asignaciones.edit', compact('asignacion', 'grupos', 'cursos', 'docentes'));
    }

    public function updateAsignacion(Request $request, $id)
    {
        $request->validate([
            'grupo_id' => 'required|exists:grupos,id_grupo',
            'curso_id' => 'required|exists:cursos,id_curso',
            'docente_id' => 'required|exists:usuarios,id',
            'dia_semana' => 'required',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
        ]);
        \DB::table('asignaciones')->where('id_asignacion', $id)->update([
            'grupo_id' => $request->grupo_id,
            'curso_id' => $request->curso_id,
            'docente_id' => $request->docente_id,
            'dia_semana' => $request->dia_semana,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
        ]);
        return redirect()->route('admin.asignaciones.index')->with('success', 'Asignación actualizada correctamente.');
    }

    public function destroyAsignacion($id)
    {
        \DB::table('asignaciones')->where('id_asignacion', $id)->delete();
        return redirect()->route('admin.asignaciones.index')->with('success', 'Asignación eliminada correctamente.');
    }
    
    public function updateUsuario(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'required|string',
            'apellido' => 'required|string',
            'usuario' => 'required|string',
            'email' => 'required|email',
            'rol_id' => 'required|integer',
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
        
        \DB::table('usuarios')->where('id', $id)->update($updateData);
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado correctamente.');
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

        \DB::table('usuarios')->insert([
            'nombre' => $validated['nombre'],
            'apellido' => $validated['apellido'],
            'usuario' => $validated['usuario'],
            'email' => $validated['email'],
            'password' => password_hash($validated['password'], PASSWORD_DEFAULT),
            'rol_id' => $validated['rol_id'],
            'genero' => $validated['genero'],
        ]);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario creado correctamente.');
    }
}
