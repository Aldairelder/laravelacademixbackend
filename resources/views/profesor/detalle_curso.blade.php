@extends('layouts.profesor')

@section('content')
<main class="container py-4">
    <h2>Detalle del Curso</h2>
    {{-- Aquí va el contenido de detalle del curso --}}

    <ul class="nav nav-tabs flex-wrap mb-3 shadow-sm rounded overflow-hidden">
        <li class="nav-item flex-fill text-center">
            <button class="nav-link active w-100" data-bs-toggle="tab" data-bs-target="#materiales">📚 Materiales</button>
        </li>
        <li class="nav-item flex-fill text-center">
            <button class="nav-link w-100" data-bs-toggle="tab" data-bs-target="#evaluaciones">📝 Evaluaciones</button>
        </li>
        <li class="nav-item flex-fill text-center">
            <button class="nav-link w-100" data-bs-toggle="tab" data-bs-target="#foros">💬 Foros</button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Materiales -->
        <div class="tab-pane fade show active" id="materiales">
            <form method="POST" enctype="multipart/form-data" class="card card-body mb-4 shadow-sm">
                @csrf
                <input name="titulo" class="form-control mb-2" placeholder="Título" required value="{{ old('titulo', $edit_material->titulo ?? '') }}">
                <textarea name="descripcion" class="form-control mb-2" placeholder="Descripción" required>{{ old('descripcion', $edit_material->descripcion ?? '') }}</textarea>
                <input type="file" name="archivo" class="form-control mb-2" accept="application/pdf">
                <div class="d-flex align-items-center">
                    <button class="btn btn-success">{{ isset($edit_material) ? 'Actualizar Material' : '📤 Subir Material' }}</button>
                    @if(isset($edit_material))
                        <a href="?tab=materiales#materiales" class="btn btn-secondary ms-2">Cancelar</a>
                    @endif
                </div>
            </form>
            @if($materiales->count())
                <ul class="list-group">
                    @foreach($materiales as $m)
                        <li class="list-group-item">
                            <strong>{{ $m->titulo }}</strong><br>
                            {{ $m->descripcion }}<br>
                            @if($m->archivo && file_exists(public_path('storage/'.$m->archivo)))
                                <button class="btn btn-outline-primary btn-sm mt-2" onclick="window.open('{{ asset('storage/'.$m->archivo) }}', '_blank')">📎 Ver archivo</button>
                            @elseif($m->archivo)
                                <span class="badge bg-danger mt-2">
                                    Archivo no disponible<br>
                                    <small>{{ 'storage/'.$m->archivo }}</small>
                                </span>
                            @endif
                            <div class="text-muted"><small>📅 {{ $m->fecha_subida }}</small></div>
                            <a href="?edit_material={{ $m->id_material }}&tab=materiales#materiales" class="btn btn-outline-warning btn-sm mt-2"><i class="fa fa-edit"></i> Editar</a>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="alert alert-warning mt-3">No hay materiales.</div>
            @endif
        </div>

        <!-- Evaluaciones -->
        <div class="tab-pane fade" id="evaluaciones">
            @if ($errors->has('titulo') || $errors->has('descripcion') || $errors->has('fecha') || $errors->has('archivo'))
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            @if(str_contains($error, 'descripcion') || str_contains($error, 'titulo') || str_contains($error, 'fecha') || str_contains($error, 'archivo'))
                                <li>{{ $error }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" enctype="multipart/form-data" class="card card-body mb-4 shadow-sm">
                @csrf
                @if(isset($edit_eval))
                    <input type="hidden" name="editar_evaluacion" value="1">
                    <input type="hidden" name="eval_id" value="{{ $edit_eval->id_evaluacion }}">
                @else
                    <input type="hidden" name="guardar_evaluacion" value="1">
                @endif
                <input name="titulo" class="form-control mb-2" placeholder="Título" required value="{{ old('titulo', $edit_eval->titulo ?? '') }}">
                <textarea name="descripcion" class="form-control mb-2" placeholder="Descripción" required>{{ old('descripcion', $edit_eval->descripcion ?? '') }}</textarea>
                <input type="date" name="fecha" class="form-control mb-2" required value="{{ old('fecha', $edit_eval->fecha ?? '') }}">
                <input type="file" name="archivo" class="form-control mb-2" accept="application/pdf">
                <div class="d-flex align-items-center">
                    <button class="btn btn-warning">{{ isset($edit_eval) ? 'Actualizar Evaluación' : '📝 Crear Evaluación' }}</button>
                    @if(isset($edit_eval))
                        <a href="?tab=evaluaciones#evaluaciones" class="btn btn-secondary ms-2">Cancelar</a>
                    @endif
                </div>
            </form>
            @if($evaluaciones->count())
                <ul class="list-group">
                    @foreach($evaluaciones as $e)
                        <li class="list-group-item">
                            <strong>{{ $e->titulo }}</strong><br>
                            {{ $e->descripcion }}<br>
                            <small class="text-muted">📅 {{ $e->fecha }}</small><br>
                            @if($e->archivo && file_exists(public_path('storage/' . $e->archivo)))
                                <a href="{{ asset('storage/' . $e->archivo) }}" target="_blank" class="btn btn-outline-primary btn-sm mt-2">📎 Ver archivo</a>
                            @elseif($e->archivo)
                                <span class="badge bg-danger mt-2">
                                    Archivo no disponible<br>
                                    <small>{{ 'storage/' . $e->archivo }}</small>
                                </span>
                            @endif

                            
                            <a href="{{ route('profesor.ver_entregas', $e->id_evaluacion) }}" class="btn btn-outline-secondary btn-sm mt-2">📂 Ver entregas</a>
                            <a href="?edit_eval={{ $e->id_evaluacion }}&tab=evaluaciones" class="btn btn-outline-warning btn-sm mt-2"><i class="fa fa-edit"></i> Editar</a>
                            <span class="badge bg-primary">{{ $e->pendientes ?? 0 }} pendientes</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="alert alert-warning">No hay evaluaciones registradas.</div>
            @endif
        </div>

        <!-- Foros -->
        <div class="tab-pane fade" id="foros" role="tabpanel">
    @if ($errors->has('titulo') || $errors->has('contenido') || $errors->has('archivo'))
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    @if(str_contains($error, 'contenido') || str_contains($error, 'titulo') || str_contains($error, 'archivo'))
                        <li>{{ $error }}</li>
                    @endif
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" enctype="multipart/form-data" class="card card-body mb-4 shadow-sm">
        @csrf
        @if(isset($edit_foro))
            <input type="hidden" name="editar_foro" value="1">
            <input type="hidden" name="edit_foro" value="{{ $edit_foro->id_foro }}">
        @else
            <input type="hidden" name="guardar_foro" value="1">
        @endif
        <input name="titulo" class="form-control mb-2" placeholder="Título" required value="{{ old('titulo', $edit_foro->titulo ?? '') }}">
        <textarea name="contenido" class="form-control mb-2" placeholder="Contenido" required>{{ old('contenido', $edit_foro->contenido ?? '') }}</textarea>
        <input type="file" name="archivo" class="form-control mb-2" accept="application/pdf">
        <div class="d-flex align-items-center">
            <button class="btn btn-info">{{ isset($edit_foro) ? 'Actualizar Foro' : '💬 Crear Foro' }}</button>
            @if(isset($edit_foro))
                <a href="?tab=foros#foros" class="btn btn-secondary ms-2">Cancelar</a>
            @endif
        </div>
    </form>

    @if($foros->count())
        <ul class="list-group">
            @foreach($foros as $f)
                <li class="list-group-item">
                    <strong>{{ $f->titulo }}</strong><br>
                    {{ $f->contenido }}<br>
                    <small class="text-muted">📅 {{ $f->fecha_publicacion }}</small><br>

                    @if($f->archivo && file_exists(public_path('storage/' . $f->archivo)))
                        <a href="{{ asset('storage/' . $f->archivo) }}" target="_blank" class="btn btn-outline-primary btn-sm mt-2">📎 Ver archivo</a>
                    @elseif($f->archivo)
                        <span class="badge bg-danger mt-2">
                            Archivo no disponible<br>
                            <small>{{ 'storage/' . $f->archivo }}</small>
                        </span>
                    @endif

                    <a href="{{ route('profesor.ver_respuestas', $f->id_foro) }}" class="btn btn-outline-info btn-sm mt-2">📂 Ver respuestas</a>
                    <a href="?edit_foro={{ $f->id_foro }}&tab=foros" class="btn btn-outline-warning btn-sm mt-2"><i class="fa fa-edit"></i> Editar</a>
                    <span class="badge bg-info text-dark">{{ $f->pendientes ?? 0 }} pendientes</span>
                </li>
            @endforeach
        </ul>
    @else
        <div class="alert alert-warning">No hay foros aún.</div>
    @endif
</div>

    </div>
</main>
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
    // Activar tab según parámetro
    const params = new URLSearchParams(window.location.search);
    const tab = params.get('tab');
    if(tab) {
      const tabBtn = document.querySelector([data-bs-target="#${tab}"]);
      if(tabBtn) tabBtn.click();
    }
    // Cambiar la URL al cambiar de tab
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(btn => {
      btn.addEventListener('shown.bs.tab', function (e) {
        const newTab = e.target.getAttribute('data-bs-target').replace('#', '');
        const url = new URL(window.location);
        url.searchParams.set('tab', newTab);
        window.history.replaceState({}, '', url);
      });
    });
  });
  function cambiarTema() {
    const actual = localStorage.getItem("tema") || "claro";
    localStorage.setItem("tema", actual === "oscuro" ? "claro" : "oscuro");
    location.reload();
  }
</script>
@endpush