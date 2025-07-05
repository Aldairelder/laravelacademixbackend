@php
if (session_status() === PHP_SESSION_NONE) session_start();
@endphp
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm" style="background: linear-gradient(135deg, #4361ee, #3f37c9);">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center fw-bold" href="{{ route('estudiante.panel') }}">
            <i class="fas fa-chalkboard-teacher me-2 fs-4"></i>
            <span class="d-none d-sm-inline">Panel</span> Estudiante
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarEstudiante">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarEstudiante">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active d-flex align-items-center" href="{{ route('estudiante.cursos') }}" style="font-weight: 500;">
                        <i class="fas fa-book me-2"></i>
                        <span class="d-none d-sm-inline">Mis Cursos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center" href="{{ route('estudiante.calendario') }}" style="font-weight: 500;">
                        <i class="fas fa-calendar-alt me-2"></i>
                        <span class="d-none d-sm-inline">Horario </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center" href="{{ route('estudiante.notas') }}" style="font-weight: 500;">
                        <i class="fas fa-users me-2"></i>
                        <span class="d-none d-sm-inline">NOTAS</span>
                    </a>
                </li>
            </ul>
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="me-2 text-end d-none d-lg-block">
                            <div class="fw-semibold">{{ session('nombre') }}</div>
                            <small class="text-white-50">Estudiante</small>
                        </div>
                        <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fas fa-user fs-5"></i>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownUser">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user-cog me-2"></i> Perfil</a></li>
                        <li><a class="dropdown-item" href="{{ route('estudiante.configuracion') }}"><i class="fas fa-cog me-2"></i> Configuración</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i> Cerrar sesión</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
