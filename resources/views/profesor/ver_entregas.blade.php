@extends('layouts.profesor')

@section('content')
<main class="container-fluid py-4 px-3 px-md-5">
    <div class="mb-3">
        <a href="{{ route('profesor.detalle_curso', ['grupo_id' => $grupo_id, 'curso_id' => $curso_id]) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver al panel
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="card-title mb-2">{{ $evaluacion->titulo }}</h4>
            <p class="mb-2">{{ $evaluacion->descripcion }}</p>
            @if($evaluacion->archivo)
                <a href="{{ asset('storage/' . $evaluacion->archivo) }}" target="_blank">📎 Archivo evaluación</a><br>
            @endif
            <small class="text-muted">📅 Fecha: {{ $evaluacion->fecha }}</small>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">Entregas de estudiantes</h5>
            @if($entregas->count())
                <div class="list-group list-group-flush">
                    @foreach($entregas as $e)
                        <div class="list-group-item">
                            <div class="d-flex flex-column flex-md-row justify-content-between">
                                <div>
                                    <strong>{{ $e->nombre }} {{ $e->apellido }}</strong><br>
                                    {{ $e->descripcion }}<br>
                                    @if($e->archivo)
                                        <a href="{{ asset('storage/uploads/entregas/' . $e->archivo) }}" target="_blank">📎 Ver entrega</a><br>
                                    @endif
                                    @if(isset($e->nota))
                                        <div class="text-success fw-bold mt-2">Nota registrada: {{ $e->nota }}</div>
                                    @endif
                                </div>
                                <div class="text-md-end mt-2 mt-md-0">
                                    <div class="mb-2">📅 {{ $e->fecha_entrega }}</div>
                                    <form method="POST" class="d-flex align-items-center gap-2">
                                        @csrf
                                        <input type="hidden" name="estudiante_id" value="{{ $e->estudiante_id }}">
                                        <input type="number" name="nota" step="0.01" min="0" max="20" value="{{ old('nota', $e->nota) }}" class="form-control form-control-sm w-auto" placeholder="Nota (ej: 18.5)" required>
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">Ningún estudiante ha entregado aún.</div>
            @endif
        </div>
    </div>
</main>
@endsection