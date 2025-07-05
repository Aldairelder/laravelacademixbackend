@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Matrículas</h2>
    <a href="{{ route('admin.matriculas.create') }}" class="btn btn-success mb-3"><i class="fas fa-plus"></i> Nueva matrícula</a>
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
                    <th>ID Matrícula</th>
                    <th>Estudiante</th>
                    <th>Grupo</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($matriculas as $m)
                <tr>
                    <td>{{ $m->id_matricula }}</td>
                    <td>{{ $m->nombre }} {{ $m->apellido }}</td>
                    <td>{{ $m->grupo }}</td>
                    <td>{{ $m->fecha }}</td>
                    <td>
                        <a href="{{ route('admin.matriculas.edit', $m->id_matricula) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.matriculas.destroy', $m->id_matricula) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar matrícula?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
