@extends('layouts.app')

@section('content')
@include('estudiante.navbar')
<div class="container mt-5">
    <h2 class="mb-4"><i class="fas fa-user-graduate me-2"></i>Panel Estudiante</h2>
    <div class="alert alert-info">Bienvenido, {{ session('nombre') }}. Aquí puedes ver tus cursos y notas.</div>
    <div class="row g-4 mt-4">
        <div class="col-md-4">
            <a href="{{ route('estudiante.cursos') }}" class="card card-body text-center shadow-sm h-100 text-decoration-none">
                <i class="fas fa-book fa-2x mb-2 text-primary"></i>
                <span class="fw-bold">Mis Cursos</span>
            </a>
        </div>
        
    </div>
</div>
@endsection
