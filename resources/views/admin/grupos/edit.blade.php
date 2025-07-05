@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Editar Grupo</h2>
    <form action="{{ route('admin.grupos.update', $grupo->id_grupo) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="row g-3">
            <div class="col-md-6">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" name="nombre" id="nombre" class="form-control" value="{{ $grupo->nombre }}" required>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Guardar Cambios</button>
            <a href="{{ route('admin.grupos.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
