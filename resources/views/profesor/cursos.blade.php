@extends('layouts.profesor')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4"><i class="fas fa-chalkboard-teacher me-2"></i>Mis Cursos Asignados</h2>
    <div class="row">
        @forelse($asignaciones as $asignacion)
            <div class="col-md-4">
                <div class="card mb-3 shadow">
                    <div class="card-body">
                        <h5 class="card-title">{{ $asignacion->curso->nombre }}</h5>
                        <p class="card-text"><strong>Grupo:</strong> {{ $asignacion->grupo->nombre }}</p>
                        <a href="{{ route('profesor.detalle_curso', ['grupo_id' => $asignacion->grupo_id, 'curso_id' => $asignacion->curso_id]) }}" class="btn btn-primary">
                            📂 Gestionar Curso
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-warning">No tienes cursos asignados.</div>
        @endforelse
    </div>
</div>
<div class="text-end mb-3">
  <button onclick="cambiarTema()" class="btn btn-outline-secondary btn-sm">
    🌗 Cambiar tema
  </button>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const tema = localStorage.getItem("tema") || "claro";
    const isOscuro = tema === "oscuro";
    document.body.classList.remove("bg-light", "bg-dark", "text-white");
    if (isOscuro) {
      document.body.classList.add("bg-dark", "text-white");
      document.querySelectorAll(".card, .list-group-item, .form-control, .alert").forEach(el => {
        el.classList.add("bg-dark", "text-white", "border-secondary");
      });
    }
  });
  function cambiarTema() {
    const actual = localStorage.getItem("tema") || "claro";
    localStorage.setItem("tema", actual === "oscuro" ? "claro" : "oscuro");
    location.reload();
  }
</script>
@endpush
