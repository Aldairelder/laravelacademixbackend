@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Registrar Rol</h2>
    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Registrar</button>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
