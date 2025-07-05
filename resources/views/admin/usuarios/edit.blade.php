@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Editar Usuario</h2>
    <form action="{{ route('admin.usuarios.update', $usuario->id) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="row g-3">
            <div class="col-md-6">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" name="nombre" id="nombre" class="form-control" value="{{ $usuario->nombre }}" required>
            </div>
            <div class="col-md-6">
                <label for="apellido" class="form-label">Apellido</label>
                <input type="text" name="apellido" id="apellido" class="form-control" value="{{ $usuario->apellido }}" required>
            </div>
            <div class="col-md-6">
                <label for="usuario" class="form-label">Usuario</label>
                <input type="text" name="usuario" id="usuario" class="form-control" value="{{ $usuario->usuario }}" required>
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ $usuario->email }}" required>
            </div>
            <div class="col-md-6">
                <label for="password" class="form-label">Contraseña (dejar vacío para no cambiar)</label>
                <input type="password" name="password" id="password" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="rol_id" class="form-label">Rol</label>
                <select name="rol_id" id="rol_id" class="form-select" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($roles as $rol)
                        <option value="{{ $rol->id }}" {{ $usuario->rol_id == $rol->id ? 'selected' : '' }}>{{ $rol->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="genero" class="form-label">Género</label>
                <select name="genero" id="genero" class="form-select" required>
                    <option value="">-- Seleccione --</option>
                    <option value="M" {{ $usuario->genero == 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ $usuario->genero == 'F' ? 'selected' : '' }}>Femenino</option>
                    <option value="Otro" {{ $usuario->genero == 'Otro' ? 'selected' : '' }}>Otro</option>
                </select>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Guardar Cambios</button>
            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
