<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfesorController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('profesor/cursos', [ProfesorController::class, 'cursos'])->name('profesor.cursos');
Route::match(['get','post'], 'profesor/cursos/{grupo_id}/{curso_id}', [App\Http\Controllers\ProfesorController::class, 'detalleCurso'])->name('profesor.detalle_curso');
Route::get('profesor/entregas/{evaluacion_id}', [ProfesorController::class, 'verEntregas'])->name('profesor.ver_entregas');
Route::post('profesor/entregas/{evaluacion_id}', [ProfesorController::class, 'guardarNotaEntrega'])->name('profesor.guardar_nota_entrega');
Route::get('profesor/foros/{foro_id}/respuestas', [ProfesorController::class, 'verRespuestas'])->name('profesor.ver_respuestas');
Route::post('profesor/foros/{foro_id}/respuestas', [ProfesorController::class, 'guardarNotaRespuesta'])->name('profesor.guardar_nota_respuesta');
Route::match(['get', 'post'], 'profesor/configuracion', [ProfesorController::class, 'configuracion'])->name('profesor.configuracion');
Route::match(['get', 'post'], 'profesor/asistencia/{grupo_id}/{curso_id}', [ProfesorController::class, 'asistencia'])->name('profesor.asistencia');
Route::get('admin/panel', function() {
    return view('admin.panel');
})->name('admin.panel');
Route::get('admin/usuarios', [AdminController::class, 'usuariosIndex'])->name('admin.usuarios.index');
Route::get('admin/usuarios/create', [App\Http\Controllers\AdminController::class, 'createUsuario'])->name('admin.usuarios.create');
Route::post('admin/usuarios', [App\Http\Controllers\AdminController::class, 'storeUsuario'])->name('admin.usuarios.store');
Route::get('admin/usuarios/{id}/edit', [App\Http\Controllers\AdminController::class, 'editUsuario'])->name('admin.usuarios.edit');
Route::delete('admin/usuarios/{id}', [App\Http\Controllers\AdminController::class, 'destroyUsuario'])->name('admin.usuarios.destroy');
Route::patch('admin/usuarios/{id}', [App\Http\Controllers\AdminController::class, 'updateUsuario'])->name('admin.usuarios.update');
Route::get('admin/matriculas', [App\Http\Controllers\AdminController::class, 'matriculasIndex'])->name('admin.matriculas.index');
Route::get('admin/matriculas/create', [App\Http\Controllers\AdminController::class, 'createMatricula'])->name('admin.matriculas.create');
Route::get('admin/matriculas/{id}/edit', [App\Http\Controllers\AdminController::class, 'editMatricula'])->name('admin.matriculas.edit');
Route::post('admin/matriculas', [App\Http\Controllers\AdminController::class, 'storeMatricula'])->name('admin.matriculas.store');
Route::patch('admin/matriculas/{id}', [App\Http\Controllers\AdminController::class, 'updateMatricula'])->name('admin.matriculas.update');
Route::delete('admin/matriculas/{id}', [App\Http\Controllers\AdminController::class, 'destroyMatricula'])->name('admin.matriculas.destroy');
Route::get('admin/cursos', [App\Http\Controllers\AdminController::class, 'cursosIndex'])->name('admin.cursos.index');
Route::get('admin/cursos/create', [App\Http\Controllers\AdminController::class, 'createCurso'])->name('admin.cursos.create');
Route::get('admin/cursos/{id}/edit', [App\Http\Controllers\AdminController::class, 'editCurso'])->name('admin.cursos.edit');
Route::delete('admin/cursos/{id}', [App\Http\Controllers\AdminController::class, 'destroyCurso'])->name('admin.cursos.destroy');
Route::post('admin/cursos', [App\Http\Controllers\AdminController::class, 'storeCurso'])->name('admin.cursos.store');
Route::patch('admin/cursos/{id}', [App\Http\Controllers\AdminController::class, 'updateCurso'])->name('admin.cursos.update');
Route::get('admin/roles', [App\Http\Controllers\AdminController::class, 'rolesIndex'])->name('admin.roles.index');
Route::get('admin/roles/create', [App\Http\Controllers\AdminController::class, 'createRol'])->name('admin.roles.create');
Route::post('admin/roles', [App\Http\Controllers\AdminController::class, 'storeRol'])->name('admin.roles.store');
Route::get('admin/roles/{id}/edit', [App\Http\Controllers\AdminController::class, 'editRol'])->name('admin.roles.edit');
Route::patch('admin/roles/{id}', [App\Http\Controllers\AdminController::class, 'updateRol'])->name('admin.roles.update');
Route::delete('admin/roles/{id}', [App\Http\Controllers\AdminController::class, 'destroyRol'])->name('admin.roles.destroy');
Route::get('admin/grupos', [App\Http\Controllers\AdminController::class, 'gruposIndex'])->name('admin.grupos.index');
Route::get('admin/grupos/create', [App\Http\Controllers\AdminController::class, 'createGrupo'])->name('admin.grupos.create');
Route::post('admin/grupos', [App\Http\Controllers\AdminController::class, 'storeGrupo'])->name('admin.grupos.store');
Route::get('admin/grupos/{id}/edit', [App\Http\Controllers\AdminController::class, 'editGrupo'])->name('admin.grupos.edit');
Route::patch('admin/grupos/{id}', [App\Http\Controllers\AdminController::class, 'updateGrupo'])->name('admin.grupos.update');
Route::delete('admin/grupos/{id}', [App\Http\Controllers\AdminController::class, 'destroyGrupo'])->name('admin.grupos.destroy');
Route::get('admin/asignaciones', [App\Http\Controllers\AdminController::class, 'asignacionesIndex'])->name('admin.asignaciones.index');
Route::get('admin/asignaciones/create', [App\Http\Controllers\AdminController::class, 'createAsignacion'])->name('admin.asignaciones.create');
Route::post('admin/asignaciones', [App\Http\Controllers\AdminController::class, 'storeAsignacion'])->name('admin.asignaciones.store');
Route::get('admin/asignaciones/{id}/edit', [App\Http\Controllers\AdminController::class, 'editAsignacion'])->name('admin.asignaciones.edit');
Route::patch('admin/asignaciones/{id}', [App\Http\Controllers\AdminController::class, 'updateAsignacion'])->name('admin.asignaciones.update');
Route::delete('admin/asignaciones/{id}', [App\Http\Controllers\AdminController::class, 'destroyAsignacion'])->name('admin.asignaciones.destroy');
Route::get('estudiante/panel', [App\Http\Controllers\EstudianteController::class, 'panelEstudiante'])->name('estudiante.panel');
Route::get('estudiante/cursos', [App\Http\Controllers\EstudianteController::class, 'cursosEstudiante'])->name('estudiante.cursos');
Route::get('estudiante/calendario', [App\Http\Controllers\EstudianteController::class, 'calendario'])->name('estudiante.calendario');
Route::match(['get','post'], 'estudiante/curso/{grupo_id}/{curso_id}', [App\Http\Controllers\EstudianteController::class, 'detalleCurso'])->name('estudiante.detalle_curso');
Route::match(['get', 'post'], 'estudiante/configuracion', [App\Http\Controllers\EstudianteController::class, 'configuracion'])->name('estudiante.configuracion');
Route::get('estudiante/notas', [App\Http\Controllers\EstudianteController::class, 'notas'])->name('estudiante.notas');
