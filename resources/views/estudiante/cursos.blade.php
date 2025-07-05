@extends('layouts.app')

@section('content')
@include('estudiante.navbar')
<main class="container mt-5">
    <h2 class="mb-4"><i class="fas fa-graduation-cap me-2"></i>Mis Cursos Matriculados</h2>
    <div class="row">
        @foreach($cursos as $curso)
            <div class="col-md-4">
                <div class="card mb-3 shadow">
                    <div class="card-body">
                        <h5 class="card-title">{{ $curso->curso }}</h5>
                        <p class="card-text"><strong>Grupo:</strong> {{ $curso->grupo }}</p>
                        <a href="{{ route('estudiante.detalle_curso', ['grupo_id' => $curso->id_grupo, 'curso_id' => $curso->id_curso]) }}" class="btn btn-primary">
                            📂 Ver Curso
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</main>
@endsection
