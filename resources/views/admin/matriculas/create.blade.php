@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Registrar Matrícula</h2>
    <form action="{{ route('admin.matriculas.store') }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label for="estudiante_id" class="form-label">Estudiante</label>
                <select name="estudiante_id" id="estudiante_id" class="form-select" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($estudiantes as $e)
                        <option value="{{ $e->id }}">{{ $e->nombre }} {{ $e->apellido }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="grupo_id" class="form-label">Grupo</label>
                <select name="grupo_id" id="grupo_id" class="form-select" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($grupos as $g)
                        <option value="{{ $g->id_grupo }}">{{ $g->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" name="fecha" id="fecha" class="form-control" required>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Registrar</button>
            <a href="{{ route('admin.matriculas.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
