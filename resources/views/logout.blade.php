@extends('layouts.profesor')

@section('content')
<div class="container py-5 text-center">
    <div class="alert alert-success display-4 mb-4">
        <i class="fas fa-check-circle fa-2x mb-2"></i><br>
        ¡Sesión cerrada exitosamente!
    </div>
    <a href="{{ route('login') }}" class="btn btn-primary btn-lg mt-3">
        <i class="fas fa-sign-in-alt me-2"></i> Volver a iniciar sesión
    </a>
</div>
@endsection
