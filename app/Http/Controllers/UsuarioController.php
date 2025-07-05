<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index()
    {
        // Código para mostrar la lista de usuarios
    }

    public function create()
    {
        // Código para mostrar el formulario de creación de un nuevo usuario
    }

    public function store(Request $request)
    {
        // Código para guardar un nuevo usuario en la base de datos
    }

    public function show($id)
    {
        // Código para mostrar los detalles de un usuario específico
    }

    public function edit($id)
    {
        // Código para mostrar el formulario de edición de un usuario existente
    }

    public function update(Request $request, $id)
    {
        // Código para actualizar un usuario existente en la base de datos
    }

    public function destroy($id)
    {
        // Código para eliminar un usuario de la base de datos
    }
}