@extends('layouts.admin')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Panel de Administración</h2>
    <div class="row g-4">
        <div class="col-md-3">
            <a href="{{ route('admin.usuarios.index') }}" class="card card-body text-center shadow-sm h-100 text-decoration-none">
                <i class="fas fa-users fa-2x mb-2 text-primary"></i>
                <span class="fw-bold">Usuarios</span>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.roles.index') }}" class="card card-body text-center shadow-sm h-100 text-decoration-none">
                <i class="fas fa-user-tie fa-2x mb-2 text-success"></i>
                <span class="fw-bold">Roles</span>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.grupos.index') }}" class="card card-body text-center shadow-sm h-100 text-decoration-none">
                <i class="fas fa-users-rectangle fa-2x mb-2 text-info"></i>
                <span class="fw-bold">Grupos</span>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.cursos.index') }}" class="card card-body text-center shadow-sm h-100 text-decoration-none">
                <i class="fas fa-book fa-2x mb-2 text-warning"></i>
                <span class="fw-bold">Cursos</span>
            </a>
        </div>
       
        
        <div class="col-md-3">
            <a href="#" class="card card-body text-center shadow-sm h-100 text-decoration-none">
                <i class="fas fa-user-check fa-2x mb-2 text-success"></i>
                <span class="fw-bold">Asistencia</span>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.matriculas.index') }}" class="card card-body text-center shadow-sm h-100 text-decoration-none">
                <i class="fas fa-clipboard-list fa-2x mb-2 text-info"></i>
                <span class="fw-bold">Matriculas</span>
            </a>
        </div>
        <div class="col-md-3">
            <a href="#" class="card card-body text-center shadow-sm h-100 text-decoration-none">
                <i class="fas fa-envelope fa-2x mb-2 text-warning"></i>
                <span class="fw-bold">Mensajes</span>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.asignaciones.index') }}" class="card card-body text-center shadow-sm h-100 text-decoration-none">
                <i class="fas fa-link fa-2x mb-2 text-secondary"></i>
                <span class="fw-bold">Asignaciones</span>
            </a>
        </div>
    </div>
</div>
@endsection
