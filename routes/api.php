<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EstudianteApiController;
use App\Http\Controllers\Api\AdminApiController;
use App\Http\Controllers\Api\ProfesorApiController;
use App\Http\Controllers\Api\AuthApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Ruta de autenticación básica
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas de autenticación
Route::post('/login', [AuthApiController::class, 'login'])->name('login');
Route::middleware('auth:sanctum')->post('/logout', [App\Http\Controllers\Api\AuthApiController::class, 'logout']);

// Rutas para Estudiantes
Route::middleware('auth:sanctum')->prefix('estudiante')->group(function () {
    Route::get('/cursos', [EstudianteApiController::class, 'cursos']);
    Route::get('/curso/{grupo_id}/{curso_id}', [EstudianteApiController::class, 'detalleCurso']);
    Route::post('/entrega', [EstudianteApiController::class, 'subirEntrega']);
    Route::post('/foro/responder', [EstudianteApiController::class, 'responderForo']);
    Route::get('/calendario', [EstudianteApiController::class, 'calendario']);
    Route::get('/configuracion', [EstudianteApiController::class, 'configuracion']);
    Route::post('/configuracion', [EstudianteApiController::class, 'configuracion']);
    Route::get('/notas', [EstudianteApiController::class, 'notas']);
});

// Rutas para Profesores
Route::middleware('auth:sanctum')->prefix('profesor')->group(function () {
    Route::get('/cursos', [ProfesorApiController::class, 'cursos']);
    Route::get('/curso/{grupo_id}/{curso_id}', [ProfesorApiController::class, 'detalleCurso']);
    
    // Materiales
    Route::post('/material', [ProfesorApiController::class, 'guardarMaterial']);
    Route::put('/material/{id}', [ProfesorApiController::class, 'actualizarMaterial']);
    Route::delete('/material/{id}', [ProfesorApiController::class, 'eliminarMaterial']);
    
    // Evaluaciones
    Route::post('/evaluacion', [ProfesorApiController::class, 'guardarEvaluacion']);
    Route::put('/evaluacion/{id}', [ProfesorApiController::class, 'actualizarEvaluacion']);
    Route::delete('/evaluacion/{id}', [ProfesorApiController::class, 'eliminarEvaluacion']);
    
    // Foros
    Route::post('/foro', [ProfesorApiController::class, 'guardarForo']);
    Route::put('/foro/{id}', [ProfesorApiController::class, 'actualizarForo']);
    Route::delete('/foro/{id}', [ProfesorApiController::class, 'eliminarForo']);
    
    // Entregas y Respuestas
    Route::get('/entregas/{evaluacion_id}', [ProfesorApiController::class, 'verEntregas']);
    Route::get('/respuestas/{foro_id}', [ProfesorApiController::class, 'verRespuestas']);
    
    // Notas
    Route::post('/nota/entrega/{evaluacion_id}', [ProfesorApiController::class, 'guardarNotaEntrega']);
    Route::post('/nota/respuesta/{foro_id}', [ProfesorApiController::class, 'guardarNotaRespuesta']);
    
    // Asistencia
    Route::get('/asistencia/{grupo_id}/{curso_id}', [ProfesorApiController::class, 'verAsistencia']);
    Route::post('/asistencia/{grupo_id}/{curso_id}', [ProfesorApiController::class, 'guardarAsistencia']);
    
    // Perfil
    Route::get('/perfil', [ProfesorApiController::class, 'verPerfil']);
    Route::post('/perfil', [ProfesorApiController::class, 'actualizarPerfil']);
});

// Rutas para Administradores
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    // Usuarios
    Route::get('/usuarios', [AdminApiController::class, 'usuariosIndex']);
    Route::post('/usuarios', [AdminApiController::class, 'storeUsuario']);
    Route::get('/usuarios/{id}', [AdminApiController::class, 'showUsuario']);
    Route::put('/usuarios/{id}', [AdminApiController::class, 'updateUsuario']);
    Route::delete('/usuarios/{id}', [AdminApiController::class, 'destroyUsuario']);
    
    // Roles
    Route::get('/roles', [AdminApiController::class, 'rolesIndex']);
    Route::post('/roles', [AdminApiController::class, 'storeRol']);
    Route::get('/roles/{id}', [AdminApiController::class, 'showRol']);
    Route::put('/roles/{id}', [AdminApiController::class, 'updateRol']);
    Route::delete('/roles/{id}', [AdminApiController::class, 'destroyRol']);
    
    // Cursos
    Route::get('/cursos', [AdminApiController::class, 'cursosIndex']);
    Route::post('/cursos', [AdminApiController::class, 'storeCurso']);
    Route::get('/cursos/{id}', [AdminApiController::class, 'showCurso']);
    Route::put('/cursos/{id}', [AdminApiController::class, 'updateCurso']);
    Route::delete('/cursos/{id}', [AdminApiController::class, 'destroyCurso']);
    
    // Grupos
    Route::get('/grupos', [AdminApiController::class, 'gruposIndex']);
    Route::post('/grupos', [AdminApiController::class, 'storeGrupo']);
    Route::get('/grupos/{id}', [AdminApiController::class, 'showGrupo']);
    Route::put('/grupos/{id}', [AdminApiController::class, 'updateGrupo']);
    Route::delete('/grupos/{id}', [AdminApiController::class, 'destroyGrupo']);
    
    // Matrículas
    Route::get('/matriculas', [AdminApiController::class, 'matriculasIndex']);
    Route::post('/matriculas', [AdminApiController::class, 'storeMatricula']);
    Route::get('/matriculas/{id}', [AdminApiController::class, 'showMatricula']);
    Route::put('/matriculas/{id}', [AdminApiController::class, 'updateMatricula']);
    Route::delete('/matriculas/{id}', [AdminApiController::class, 'destroyMatricula']);
    
    // Asignaciones
    Route::get('/asignaciones', [AdminApiController::class, 'asignacionesIndex']);
    Route::post('/asignaciones', [AdminApiController::class, 'storeAsignacion']);
    Route::get('/asignaciones/{id}', [AdminApiController::class, 'showAsignacion']);
    Route::put('/asignaciones/{id}', [AdminApiController::class, 'updateAsignacion']);
    Route::delete('/asignaciones/{id}', [AdminApiController::class, 'destroyAsignacion']);
});
