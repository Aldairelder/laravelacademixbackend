@extends('layouts.app')

@section('content')
@include('estudiante.navbar')
<main class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <img src="{{ asset('img/descarga.png') }}" alt="Logo" style="height: 90px; width:auto; margin-right:20px;">
        <div>
            <h4 class="mb-0 fw-bold">Institución Educativa Ejemplo</h4>
            <div class="text-muted">Dirección: Calle Ficticia 123, Ciudad - Tel: 555-1234</div>
        </div>
    </div>
    <div class="mb-3 text-end">
        <button class="btn btn-outline-primary print-hide" onclick="window.print()"><i class="fa fa-print me-1"></i> Imprimir</button>
    </div>
    <h2 class="mb-4 fw-bold text-primary-emphasis"><i class="fa fa-graduation-cap me-2"></i>Mis Notas</h2>
    @if(count($cursos) > 0)
        @foreach($cursos as $curso)
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary bg-opacity-10 fw-bold">
                    {{ $curso['curso'] }} <span class="text-muted">({{ $curso['grupo'] }})</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tipo</th>
                                <th>Título</th>
                                <th>Descripción</th>
                                <th>Nota</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if(count($curso['evaluaciones']) > 0)
                            @foreach($curso['evaluaciones'] as $ev)
                            <tr>
                                <td><span class="badge bg-primary">Evaluación</span></td>
                                <td>{{ $ev['titulo'] }}</td>
                                <td>{{ $ev['descripcion'] }}</td>
                                <td>
                                    @if($ev['nota'])
                                        <span class="fw-bold fs-5">
                                            @php $n = strtoupper(trim($ev['nota'])); @endphp
                                            @if($n === 'C') <span class="text-danger">C <small>(Desaprobado)</small></span>
                                            @elseif($n === 'B') <span class="text-warning">B <small>(Regular)</small></span>
                                            @elseif($n === 'A') <span class="text-success">A <small>(Aprobado)</small></span>
                                            @elseif($n === 'AD') <span class="text-primary">AD <small>(Excelente)</small></span>
                                            @else <span class="text-secondary">{{ $ev['nota'] }} <small>(Sin calificar)</small></span>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-muted">Sin nota</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        @endif
                        @if(isset($curso['foros']) && count($curso['foros']) > 0)
                            @foreach($curso['foros'] as $foro)
                            <tr>
                                <td><span class="badge bg-info text-dark">Foro</span></td>
                                <td>{{ $foro['titulo'] }}</td>
                                <td>{{ $foro['descripcion'] }}</td>
                                <td>
                                    @if($foro['nota'])
                                        <span class="fw-bold fs-5">
                                            @php $n = strtoupper(trim($foro['nota'])); @endphp
                                            @if($n === 'C') <span class="text-danger">C <small>(Desaprobado)</small></span>
                                            @elseif($n === 'B') <span class="text-warning">B <small>(Regular)</small></span>
                                            @elseif($n === 'A') <span class="text-success">A <small>(Aprobado)</small></span>
                                            @elseif($n === 'AD') <span class="text-primary">AD <small>(Excelente)</small></span>
                                            @else <span class="text-secondary">{{ $foro['nota'] }} <small>(Sin calificar)</small></span>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-muted">Sin nota</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        @endif
                        @if(count($curso['evaluaciones']) == 0 && (!isset($curso['foros']) || count($curso['foros']) == 0))
                            <tr><td colspan="4" class="text-center text-muted">No hay evaluaciones ni foros registrados.</td></tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-info">No estás matriculado en ningún curso.</div>
    @endif
    <a href="{{ route('estudiante.cursos') }}" class="btn btn-secondary mt-3"><i class="fa fa-arrow-left me-2"></i>Volver a Cursos</a>
</main>
@endsection

@push('styles')
<style>
@media print {
    .print-hide { display: none !important; }
}
</style>
@endpush
