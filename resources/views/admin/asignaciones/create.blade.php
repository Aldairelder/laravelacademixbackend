@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Registrar Asignación</h2>
    <form action="{{ route('admin.asignaciones.store') }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-md-4">
                <label for="grupo_id" class="form-label">Grupo</label>
                <select name="grupo_id" id="grupo_id" class="form-select" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($grupos as $g)
                        <option value="{{ $g->id_grupo }}">{{ $g->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="curso_id" class="form-label">Curso</label>
                <select name="curso_id" id="curso_id" class="form-select" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($cursos as $c)
                        <option value="{{ $c->id_curso }}">{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="docente_id" class="form-label">Docente</label>
                <select name="docente_id" id="docente_id" class="form-select" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($docentes as $d)
                        <option value="{{ $d->id }}">{{ $d->nombre }} {{ $d->apellido }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="dia_semana" class="form-label">Día de la semana</label>
                <select name="dia_semana" id="dia_semana" class="form-select" required>
                    <option value="">-- Seleccione --</option>
                    <option value="Lunes">Lunes</option>
                    <option value="Martes">Martes</option>
                    <option value="Miércoles">Miércoles</option>
                    <option value="Jueves">Jueves</option>
                    <option value="Viernes">Viernes</option>
                    <option value="Sábado">Sábado</option>
                    <option value="Domingo">Domingo</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="hora_inicio" class="form-label">Hora inicio</label>
                <input type="time" name="hora_inicio" id="hora_inicio" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label for="hora_fin" class="form-label">Hora fin</label>
                <input type="time" name="hora_fin" id="hora_fin" class="form-control" required>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Registrar</button>
            <a href="{{ route('admin.asignaciones.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
