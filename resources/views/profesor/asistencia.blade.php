@extends('layouts.profesor')

@section('content')
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary">
            <i class="fas fa-calendar-check me-2"></i>Gestión de Asistencia
        </h2>
    </div>
    <form method="GET" class="mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">
                    <i class="fas fa-filter me-2 text-primary"></i>Filtrar por grupo y curso
                </h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="grupo_id" class="form-label">Grupo</label>
                        <select name="grupo_id" id="grupo_id" class="form-select" onchange="this.form.submit()" required>
                            <option value="">-- Seleccione grupo --</option>
                            @foreach($grupos as $g)
                                <option value="{{ $g->id_grupo }}" {{ $grupo_id == $g->id_grupo ? 'selected' : '' }}>{{ $g->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="curso_id" class="form-label">Curso</label>
                        <select name="curso_id" id="curso_id" class="form-select" onchange="this.form.submit()" required>
                            <option value="">-- Seleccione curso --</option>
                            @foreach($cursos as $c)
                                <option value="{{ $c->id_curso }}" {{ $curso_id == $c->id_curso ? 'selected' : '' }}>{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @if($grupo_id && $curso_id)
        <div class="text-end mb-4">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalFecha">
                <i class="fas fa-plus-circle me-2"></i>Registrar Asistencia
            </button>
        </div>
        <!-- Modal Selección Fecha -->
        <div class="modal fade" id="modalFecha" tabindex="-1" aria-labelledby="modalFechaLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form id="formFecha">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="modalFechaLabel">
                                <i class="fas fa-calendar-day me-2"></i>Seleccionar Fecha
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <label for="fecha_modal" class="form-label">Fecha de asistencia</label>
                            <input type="date" id="fecha_modal" name="fecha_modal" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-arrow-right me-2"></i>Continuar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Modal Lista Asistencia -->
        <div class="modal fade" id="modalAsistencia" tabindex="-1" aria-labelledby="modalAsistenciaLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <form method="POST" id="formAsistencia">
                    @csrf
                    <input type="hidden" name="guardar_asistencia" value="1">
                    <input type="hidden" name="fecha_asistencia" id="input_fecha_asistencia">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="modalAsistenciaLabel">
                                <i class="fas fa-user-check me-2"></i>Registrar Asistencia
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            @if($alumnos && count($alumnos) > 0)
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>Fecha seleccionada: <strong id="fecha-seleccionada"></strong>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead>
                                            <tr>
                                                <th>Estudiante</th>
                                                <th>Estado</th>
                                                <th>Observación</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($alumnos as $a)
                                                <tr>
                                                    <td>
                                                        {{ $a->nombre }} {{ $a->apellido }}<br>
                                                        <small class="text-muted">ID: {{ $a->id }}</small>
                                                    </td>
                                                    <td>
                                                        <select name="asistencia[{{ $a->id }}]" class="form-select" required>
                                                            <option value="Presente">✅ Presente</option>
                                                            <option value="Ausente">❌ Ausente</option>
                                                            <option value="Justificado">📝 Justificado</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="observacion[{{ $a->id }}]" class="form-control" placeholder="Observación (opcional)">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-warning text-center">
                                    <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                                    <h4>No hay alumnos en este grupo</h4>
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Asistencia
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Historial -->
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                <h5><i class="fas fa-history me-2"></i>Historial de Asistencias</h5>
            </div>
            <div class="card-body">
                @if(!empty($asistencias_por_fecha))
                    <div class="accordion" id="accordionAsistencia">
                        @foreach($asistencias_por_fecha as $fecha => $lista)
                            @php
                                $id = 'asistencia_' . $loop->index;
                                $collapse = 'collapse_' . $loop->index;
                                $fecha_legible = \Carbon\Carbon::parse($fecha)->format('d/m/Y H:i');
                                $presentes = $ausentes = $justificados = 0;
                                foreach($lista as $r) {
                                    if($r->estado == 'Presente') $presentes++;
                                    elseif($r->estado == 'Ausente') $ausentes++;
                                    elseif($r->estado == 'Justificado') $justificados++;
                                }
                            @endphp
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="{{ $id }}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapse }}">
                                        {{ $fecha_legible }} -
                                        <span class="badge bg-success mx-2">{{ $presentes }} Presente</span>
                                        <span class="badge bg-danger mx-2">{{ $ausentes }} Ausente</span>
                                        <span class="badge bg-warning text-dark">{{ $justificados }} Justificado</span>
                                    </button>
                                </h2>
                                <div id="{{ $collapse }}" class="accordion-collapse collapse" data-bs-parent="#accordionAsistencia">
                                    <div class="accordion-body">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Estudiante</th>
                                                    <th>Estado</th>
                                                    <th>Observación</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($lista as $r)
                                                    <tr>
                                                        <td>{{ $r->nombre }} {{ $r->apellido }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $r->estado == 'Presente' ? 'success' : ($r->estado == 'Ausente' ? 'danger' : 'warning text-dark') }}">
                                                                {{ $r->estado }}
                                                            </span>
                                                        </td>
                                                        <td>{!! $r->observacion ? e($r->observacion) : '<span class="text-muted">—</span>' !!}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                        <h4>No hay registros de asistencia</h4>
                    </div>
                @endif
            </div>
        </div>
    @endif
</main>
@push('scripts')
<script>
document.getElementById('formFecha')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const fecha = document.getElementById('fecha_modal').value;
    document.getElementById('input_fecha_asistencia').value = fecha;
    document.getElementById('fecha-seleccionada').textContent = new Date(fecha).toLocaleDateString('es-ES', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
    });
    const modalFecha = bootstrap.Modal.getInstance(document.getElementById('modalFecha'));
    modalFecha.hide();
    new bootstrap.Modal(document.getElementById('modalAsistencia')).show();
});
</script>
@endpush
@endsection
