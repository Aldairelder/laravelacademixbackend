@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Editar Asignación</h2>
    <form action="{{ route('admin.asignaciones.update', $asignacion->id_asignacion) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="row g-3">
            <div class="col-md-4">
                <label for="grupo_id" class="form-label">Grupo</label>
                <select name="grupo_id" id="grupo_id" class="form-select" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($grupos as $g)
                        <option value="{{ $g->id_grupo }}" {{ $asignacion->grupo_id == $g->id_grupo ? 'selected' : '' }}>{{ $g->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="curso_id" class="form-label">Curso</label>
                <select name="curso_id" id="curso_id" class="form-select" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($cursos as $c)
                        <option value="{{ $c->id_curso }}" {{ $asignacion->curso_id == $c->id_curso ? 'selected' : '' }}>{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="docente_id" class="form-label">Docente</label>
                <select name="docente_id" id="docente_id" class="form-select" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($docentes as $d)
                        <option value="{{ $d->id }}" {{ $asignacion->docente_id == $d->id ? 'selected' : '' }}>{{ $d->nombre }} {{ $d->apellido }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="dia_semana" class="form-label">Día de la semana</label>
                <select name="dia_semana" id="dia_semana" class="form-select" required>
                    <option value="">-- Seleccione --</option>
                    <option value="Lunes" {{ $asignacion->dia_semana == 'Lunes' ? 'selected' : '' }}>Lunes</option>
                    <option value="Martes" {{ $asignacion->dia_semana == 'Martes' ? 'selected' : '' }}>Martes</option>
                    <option value="Miércoles" {{ $asignacion->dia_semana == 'Miércoles' ? 'selected' : '' }}>Miércoles</option>
                    <option value="Jueves" {{ $asignacion->dia_semana == 'Jueves' ? 'selected' : '' }}>Jueves</option>
                    <option value="Viernes" {{ $asignacion->dia_semana == 'Viernes' ? 'selected' : '' }}>Viernes</option>
                    <option value="Sábado" {{ $asignacion->dia_semana == 'Sábado' ? 'selected' : '' }}>Sábado</option>
                    <option value="Domingo" {{ $asignacion->dia_semana == 'Domingo' ? 'selected' : '' }}>Domingo</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="hora_inicio" class="form-label">Hora inicio</label>
                <input type="time" name="hora_inicio" id="hora_inicio" class="form-control" value="{{ $asignacion->hora_inicio }}" required>
            </div>
            <div class="col-md-4">
                <label for="hora_fin" class="form-label">Hora fin</label>
                <input type="time" name="hora_fin" id="hora_fin" class="form-control" value="{{ $asignacion->hora_fin }}" required>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Guardar Cambios</button>
            <a href="{{ route('admin.asignaciones.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
