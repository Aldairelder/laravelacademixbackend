<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GrupoController extends Controller
{
    public function index()
    {
        // Código para mostrar la lista de grupos
    }

    public function create()
    {
        // Código para mostrar el formulario de creación de un nuevo grupo
    }

    public function store(Request $request)
    {
        // Código para guardar un nuevo grupo en la base de datos
    }

    public function show($id)
    {
        // Código para mostrar los detalles de un grupo específico
    }

    public function edit($id)
    {
        // Código para mostrar el formulario de edición de un grupo existente
    }

    public function update(Request $request, $id)
    {
        // Código para actualizar un grupo existente en la base de datos
    }

    public function destroy($id)
    {
        // Código para eliminar un grupo de la base de datos
    }
}