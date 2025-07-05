@extends('layouts.app')

@section('content')
@include('estudiante.navbar')
<div class="container mt-5">
    <h2 class="mb-4 fw-bold text-primary-emphasis"><i class="fa fa-book-open me-2"></i>{{ $grupo->curso ?? '' }} - {{ $grupo->grupo ?? '' }}</h2>
    <div class="main-card card shadow-lg border-0 mb-4 bg-body-tertiary">
      <ul class="nav nav-tabs nav-justified flex-wrap mb-0 rounded-top bg-primary-subtle" role="tablist">
          <li class="nav-item" role="presentation">
              <button class="nav-link active fw-bold text-primary" data-bs-toggle="tab" data-bs-target="#materiales" type="button" role="tab"><i class="fa fa-book me-1"></i>Materiales</button>
          </li>
          <li class="nav-item" role="presentation">
              <button class="nav-link fw-bold text-warning" data-bs-toggle="tab" data-bs-target="#evaluaciones" type="button" role="tab"><i class="fa fa-pen-to-square me-1"></i>Evaluaciones</button>
          </li>
          <li class="nav-item" role="presentation">
              <button class="nav-link fw-bold text-info" data-bs-toggle="tab" data-bs-target="#foros" type="button" role="tab"><i class="fa fa-comments me-1"></i>Foros</button>
          </li>
      </ul>
      <div class="tab-content p-4 bg-white rounded-bottom">
        <!-- Materiales -->
        <div class="tab-pane fade show active" id="materiales" role="tabpanel">
            @if(count($materiales) > 0)
                <div class="accordion" id="materialesAccordion">
                  @foreach($materiales as $m)
                    <div class="accordion-item mb-2 card-section">
                      <h2 class="accordion-header" id="headingMat{{ $m->id_material }}">
                        <button class="accordion-button collapsed fw-bold text-success bg-success bg-opacity-10 d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMat{{ $m->id_material }}" aria-expanded="false" aria-controls="collapseMat{{ $m->id_material }}">
                          <span><i class="fa fa-book me-2"></i>{{ $m->titulo }}</span>
                          <span class="text-muted small ms-2"><i class="fa fa-calendar-alt me-1"></i>{{ $m->fecha_subida }}</span>
                        </button>
                      </h2>
                      <div id="collapseMat{{ $m->id_material }}" class="accordion-collapse collapse" aria-labelledby="headingMat{{ $m->id_material }}" data-bs-parent="#materialesAccordion">
                        <div class="accordion-body">
                          <div class="mb-2"><span class="fw-bold">Descripción:</span> {{ $m->descripcion }}</div>
                          @if($m->archivo)
                        @php
                             $nombreArchivoLimpio = basename($m->archivo);
                        @endphp

                          <a href="{{ asset('storage/uploads/materiales/' . $nombreArchivoLimpio) }}" target="_blank" class="btn btn-outline-primary btn-sm mb-2">
                              <i class="fa fa-paperclip"></i> Ver archivo
                          </a>
                          <div class="mb-2">
                              <span class="badge bg-primary bg-opacity-25 text-dark">
                                  <i class="fa fa-file-pdf me-1"></i>{{ $nombreArchivoLimpio }}
                              </span>
                          </div>
                          <div class="mb-3 border rounded overflow-auto" style="height:480px;">
                              <iframe src="{{ asset('storage/uploads/materiales/' . $nombreArchivoLimpio) }}#toolbar=0" style="width:100%;height:470px;border:none;"></iframe>
                          </div>
                      @endif
                          
                          <div class="text-end"><small class="text-muted">📅 {{ $m->fecha_subida }}</small></div>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
            @else
                <div class="alert alert-success mt-3"><i class="fa fa-circle-info me-2"></i>No hay materiales disponibles.</div>
            @endif
        </div>
        <!-- Evaluaciones -->
       <div class="tab-pane fade" id="evaluaciones" role="tabpanel">
    @if(count($evaluaciones) > 0)
        <div class="accordion" id="evaluacionesAccordion">
            @foreach($evaluaciones as $e)
                @php
                $id_eval = $e->id_evaluacion;
                $entrega = $entregas[$id_eval] ?? null;
                $nota_eval = $notas['evaluacion_'.$id_eval]->nota ?? null;
                @endphp
                <div class="accordion-item mb-2 card-section">
                    <h2 class="accordion-header" id="headingEval{{ $id_eval }}">
                        <button class="accordion-button collapsed fw-bold text-warning bg-warning bg-opacity-10 d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEval{{ $id_eval }}" aria-expanded="false" aria-controls="collapseEval{{ $id_eval }}">
                            <span><i class="fa fa-pen-to-square me-2"></i>{{ $e->titulo }}</span>
                            <span class="text-muted small ms-2"><i class="fa fa-calendar-alt me-1"></i>{{ $e->fecha }}</span>
                        </button>
                    </h2>
                    <div id="collapseEval{{ $id_eval }}" class="accordion-collapse collapse" aria-labelledby="headingEval{{ $id_eval }}" data-bs-parent="#evaluacionesAccordion">
                        <div class="accordion-body">
                            <div class="mb-2"><span class="fw-bold">Descripción:</span> {{ $e->descripcion }}</div>
                            @if($e->archivo)
                                @php
                                    $nombreArchivoLimpioEval = basename($e->archivo);
                                @endphp
                                <a href="{{ asset('storage/uploads/evaluaciones/'.$nombreArchivoLimpioEval) }}" target="_blank" class="btn btn-outline-primary btn-sm mb-2"><i class="fa fa-paperclip"></i> Ver Evaluación</a><br>
                                <span class="badge bg-primary bg-opacity-25 text-dark mb-2"><i class="fa fa-file-pdf me-1"></i>{{ $nombreArchivoLimpioEval }}</span>
                                <div class="mb-3 border rounded overflow-auto" style="height:480px;">
                                    <iframe src="{{ asset('storage/uploads/evaluaciones/'.$nombreArchivoLimpioEval) }}#toolbar=0" style="width:100%;height:470px;border:none;"></iframe>
                                </div>
                            @endif
                            @if($nota_eval)
                                <div class="alert alert-info fw-bold fs-5">
                                    <i class="fa fa-star text-primary"></i>
                                    Nota obtenida: <span class="text-primary"> {{ $nota_eval }} </span>
                                </div>
                            @endif
                            <small class="text-muted">📅 {{ $e->fecha }}</small>
                            @if($entrega)
                                <div class="mt-2 p-2 bg-success bg-opacity-25 rounded">
                                    <span class="fw-bold text-success"><i class="fa fa-check-circle"></i> Entregado:</span> <a href="{{ asset('storage/uploads/entregas/'.basename($entrega->archivo)) }}" target="_blank"> {{ basename($entrega->archivo) }}</a><br>
                                    <small>📝 {{ $entrega->descripcion }}</small><br>
                                    <small>📥 {{ $entrega->fecha_entrega }}</small><br>
                                    @if(!$nota_eval)
                                    <form method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="eliminar_entrega" value="{{ $entrega->id_entrega }}">
                                        <button type="submit" class="btn btn-danger btn-sm mt-2" onclick="return confirm('¿Eliminar esta entrega?')">❌ Eliminar entrega</button>
                                    </form>
                                    @endif
                                </div>
                            @else
                                <form method="POST" enctype="multipart/form-data" class="mt-2">
                                    @csrf
                                    <input type="hidden" name="evaluacion_id" value="{{ $id_eval }}">
                                    <input type="file" name="archivo" class="form-control mb-2" accept="application/pdf" required>
                                    <textarea name="descripcion" class="form-control mb-2" placeholder="Descripción (opcional)..."></textarea>
                                    <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-upload"></i> Subir Entrega</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-warning"><i class="fa fa-circle-info me-2"></i>No hay evaluaciones asignadas.</div>
    @endif
</div>
        <!-- Foros -->
        <div class="tab-pane fade" id="foros" role="tabpanel">
    @if(count($foros) > 0)
        <div class="accordion" id="forosAccordion">
            @foreach($foros as $f)
                @php
                $id_foro = $f->id_foro;
                $nota_foro = $notas['foro_'.$id_foro]->nota ?? null;
                @endphp
                <div class="accordion-item mb-2 card-section">
                    <h2 class="accordion-header" id="headingForo{{ $id_foro }}">
                        <button class="accordion-button collapsed fw-bold text-info bg-info bg-opacity-10 d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapseForo{{ $id_foro }}" aria-expanded="false" aria-controls="collapseForo{{ $id_foro }}">
                            <span><i class="fa fa-comments me-2"></i>{{ $f->titulo }}</span>
                            <span class="text-muted small ms-2"><i class="fa fa-calendar-alt me-1"></i>{{ $f->fecha_publicacion }}</span>
                        </button>
                    </h2>
                    <div id="collapseForo{{ $id_foro }}" class="accordion-collapse collapse" aria-labelledby="headingForo{{ $id_foro }}" data-bs-parent="#forosAccordion">
                        <div class="accordion-body">
                            <div class="mb-2"><span class="fw-bold">Contenido:</span> {{ $f->contenido }}</div>
                            @if($f->archivo)
                                @php
                                    $nombreArchivoLimpioForo = basename($f->archivo);
                                @endphp
                                <a href="{{ asset('storage/uploads/foros/'.$nombreArchivoLimpioForo) }}" target="_blank" class="btn btn-outline-primary btn-sm mb-2"><i class="fa fa-paperclip"></i> Ver Archivo Adjunto</a><br>
                                <span class="badge bg-primary bg-opacity-25 text-dark mb-2"><i class="fa fa-file-pdf me-1"></i>{{ $nombreArchivoLimpioForo }}</span>
                                <div class="mb-3 border rounded overflow-auto" style="height:480px;">
                                    <iframe src="{{ asset('storage/uploads/foros/'.$nombreArchivoLimpioForo) }}#toolbar=0" style="width:100%;height:470px;border:none;"></iframe>
                                </div>
                            @endif
                            @if($nota_foro)
                                <div class="alert alert-info fw-bold fs-5 mt-3">
                                    <i class="fa fa-star text-primary"></i>
                                    Nota obtenida: <span class="text-primary"> {{ $nota_foro }} </span>
                                </div>
                            @endif
                            <h5 class="mt-4"><i class="fa fa-reply me-1"></i> Respuestas:</h5>
                            @foreach($respuestas_foro->where('foro_id', $id_foro) as $respuesta)
                                <div class="border-start border-info border-3 ps-3 mb-2 pb-2">
                                    <p class="mb-0">
                                        <span class="fw-bold">{{ $respuesta->nombre }} {{ $respuesta->apellido }}:</span> {{ $respuesta->contenido }}
                                    </p>
                                    @if($respuesta->archivo)
                                        <small><a href="{{ asset('storage/uploads/foros/'.basename($respuesta->archivo)) }}" target="_blank" class="text-muted"><i class="fa fa-paperclip me-1"></i>{{ basename($respuesta->archivo) }}</a></small><br>
                                    @endif
                                    <small class="text-muted">Publicado: {{ $respuesta->fecha_respuesta }}</small>
                                    @if($respuesta->estudiante_id == session('usuario_id') && !$nota_foro)
                                        <form method="POST" class="d-inline ms-2">
                                            @csrf
                                            <input type="hidden" name="eliminar_respuesta" value="{{ $respuesta->id_respuesta }}">
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar esta respuesta?')">❌</button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                            {{-- FORMULARIO DE RESPUESTA: Solo se muestra si NO hay nota_foro --}}
                            @if(!$nota_foro)
                                <form method="POST" enctype="multipart/form-data" class="mt-3">
                                    @csrf
                                    <input type="hidden" name="foro_id" value="{{ $id_foro }}">
                                    <textarea name="contenido" class="form-control mb-2" placeholder="Tu respuesta..." required></textarea>
                                    <input type="file" name="archivo" class="form-control mb-2" accept="application/pdf">
                                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-paper-plane"></i> Enviar Respuesta</button>
                                </form>
                            @else
                                <div class="alert alert-success mt-3">
                                    <i class="fa fa-info-circle me-1"></i> El foro ha sido revisado. No se pueden añadir más respuestas.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-warning"><i class="fa fa-circle-info me-2"></i>No hay foros disponibles.</div>
    @endif
</div>
      </div>
    </div>
    <a href="{{ route('estudiante.cursos') }}" class="btn btn-secondary mt-4"><i class="fa fa-arrow-left me-2"></i>Volver a Cursos</a>
</div>
@endsection
