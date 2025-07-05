@extends('layouts.app')

@section('content')
@include('estudiante.navbar')
<main class="container py-4">
    <h3 class="mb-4">⚙️ Configuración de cuenta</h3>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form method="POST" class="card p-4 shadow-sm bg-white">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" required value="{{ old('nombre', $usuario->nombre ?? '') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Apellido</label>
            <input type="text" name="apellido" class="form-control" required value="{{ old('apellido', $usuario->apellido ?? '') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Usuario</label>
            <input type="text" name="usuario" class="form-control" required value="{{ old('usuario', $usuario->usuario ?? '') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required value="{{ old('email', $usuario->email ?? '') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Género</label>
            <select name="genero" class="form-control" required>
                <option value="M" @if((old('genero', $usuario->genero ?? '')=='M')) selected @endif>Masculino</option>
                <option value="F" @if((old('genero', $usuario->genero ?? '')=='F')) selected @endif>Femenino</option>
                <option value="Otro" @if((old('genero', $usuario->genero ?? '')=='Otro')) selected @endif>Otro</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Nueva contraseña <small class="text-muted">(deja en blanco si no quieres cambiarla)</small></label>
            <input type="password" name="password" class="form-control" placeholder="Nueva contraseña">
        </div>
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save me-1"></i> Guardar cambios
        </button>
    </form>
</main>
@endsection
