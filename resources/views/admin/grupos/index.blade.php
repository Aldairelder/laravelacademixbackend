@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Grupos</h2>
    <a href="{{ route('admin.grupos.create') }}" class="btn btn-success mb-3"><i class="fas fa-plus"></i> Nuevo grupo</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grupos as $g)
                <tr>
                    <td>{{ $g->id_grupo }}</td>
                    <td>{{ $g->nombre }}</td>
                    <td>
                        <a href="{{ route('admin.grupos.edit', $g->id_grupo) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.grupos.destroy', $g->id_grupo) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar grupo?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
