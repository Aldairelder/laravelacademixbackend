@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Asignaciones</h2>
    <a href="{{ route('admin.asignaciones.create') }}" class="btn btn-success mb-3"><i class="fas fa-plus"></i> Nueva asignación</a>
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
                    <th>Grupo</th>
                    <th>Curso</th>
                    <th>Docente</th>
                    <th>Día</th>
                    <th>Hora inicio</th>
                    <th>Hora fin</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($asignaciones as $a)
                <tr>
                    <td>{{ $a->id_asignacion }}</td>
                    <td>{{ $a->grupo }}</td>
                    <td>{{ $a->curso }}</td>
                    <td>{{ $a->docente }}</td>
                    <td>{{ $a->dia_semana }}</td>
                    <td>{{ $a->hora_inicio }}</td>
                    <td>{{ $a->hora_fin }}</td>
                    <td>
                        <a href="{{ route('admin.asignaciones.edit', $a->id_asignacion) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.asignaciones.destroy', $a->id_asignacion) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar asignación?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
