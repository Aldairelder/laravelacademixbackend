@extends('layouts.profesor')

@section('content')
<main class="container py-4 px-3 px-md-5">
    <div class="mb-3">
        <a href="{{ route('profesor.detalle_curso', ['grupo_id' => $grupo_id, 'curso_id' => $curso_id]) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver al panel
        </a>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h4 class="card-title">{{ $foro->titulo }}</h4>
            <p class="card-text">{!! nl2br(e($foro->contenido)) !!}</p>
            @if(!empty($foro->archivo))
                <p><a href="{{ asset('storage/' . $foro->archivo) }}" target="_blank" class="btn btn-link">📎 Ver archivo del foro</a></p>
            @endif
            <small class="text-muted">📅 Publicado el {{ $foro->fecha_publicacion }}</small>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Respuestas de estudiantes</h5>
            @if($respuestas->count())
                <div class="list-group list-group-flush">
                    @foreach($respuestas as $r)
                        <div class="list-group-item">
                            <div class="row">
                                <div class="col-md-8">
                                    <p class="mb-1"><strong>{{ $r->nombre }} {{ $r->apellido }}</strong></p>
                                    <p>{!! nl2br(e($r->contenido)) !!}</p>
                                    @if($r->archivo)
                                         <p><a href="{{ asset('storage/uploads/foros/' . $r->archivo) }}" target="_blank">📎 Ver adjunto</a></p>

                                    @endif
                                    <small class="text-muted">📅 {{ $r->fecha_respuesta }}</small>
                                </div>
                                <div class="col-md-4">
                                    @if(isset($r->nota))
                                        <div class="text-success fw-bold mb-2">📝 Nota actual: {{ $r->nota }}</div>
                                    @endif
                                    <form method="POST" class="d-flex align-items-center gap-2">
                                        @csrf
                                        <input type="hidden" name="estudiante_id" value="{{ $r->estudiante_id }}">
                                        <input type="number" name="nota" step="0.01" min="0" max="20" value="{{ old('nota', $r->nota) }}" class="form-control form-control-sm w-50" placeholder="Nota (ej: 18.5)" required>
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
                <div class="alert alert-info">Ningún estudiante ha respondido aún.</div>
            @endif
        </div>
    </div>
</main>
@endsection