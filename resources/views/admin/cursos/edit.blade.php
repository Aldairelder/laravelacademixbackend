@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Editar Curso</h2>
    <form action="{{ route('admin.cursos.update', $curso->id_curso) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="row g-3">
            <div class="col-md-6">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" name="nombre" id="nombre" class="form-control" value="{{ $curso->nombre }}" required>
            </div>
            <div class="col-md-6">
                <label for="grado" class="form-label">Grado</label>
                <input type="text" name="grado" id="grado" class="form-control" value="{{ $curso->grado }}">
            </div>
            <div class="col-12">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea name="descripcion" id="descripcion" class="form-control" rows="3">{{ $curso->descripcion }}</textarea>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Guardar Cambios</button>
            <a href="{{ route('admin.cursos.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
