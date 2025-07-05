<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EvaluacionController extends Controller
{
    public function index()
    {
        // Código para mostrar una lista de evaluaciones
    }

    public function create()
    {
        // Código para mostrar el formulario de creación de una nueva evaluación
    }

    public function store(Request $request)
    {
        // Código para guardar una nueva evaluación en la base de datos
    }

    public function show($id)
    {
        // Código para mostrar los detalles de una evaluación específica
    }

    public function edit($id)
    {
        // Código para mostrar el formulario de edición de una evaluación existente
    }

    public function update(Request $request, $id)
    {
        // Código para actualizar una evaluación existente en la base de datos
    }

    public function destroy($id)
    {
        // Código para eliminar una evaluación de la base de datos
    }
}