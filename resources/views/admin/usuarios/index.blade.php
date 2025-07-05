@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Usuarios</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <a href="{{ route('admin.usuarios.create') }}" class="btn btn-success mb-3"><i class="fas fa-plus"></i> Nuevo usuario</a>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Género</th>
                    <th>Fecha creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $u)
                <tr>
                    <td>{{ $u->id }}</td>
                    <td>{{ $u->nombre }}</td>
                    <td>{{ $u->apellido }}</td>
                    <td>{{ $u->usuario }}</td>
                    <td>{{ $u->email }}</td>
                    <td>{{ $u->rol->nombre ?? '-' }}</td>
                    <td>{{ $u->genero }}</td>
                    <td>{{ $u->fecha_creacion }}</td>
                    <td>
                        <a href="{{ route('admin.usuarios.edit', $u->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.usuarios.destroy', $u->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar usuario?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
