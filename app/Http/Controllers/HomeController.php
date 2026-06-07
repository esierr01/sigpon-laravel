<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     */
    public function index()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Obtener el rol del usuario (1=admin, 2=editor, 3=visitante)
        $userRole = $user->role;

        // Opcional: obtener el nombre del rol
        $roleName = match ($userRole) {
            1 => 'Administrador',
            2 => 'Editor',
            3 => 'Visitante',
            default => 'Sin rol asignado',
        };

        // Pasar el rol y nombre del rol a la vista
        return view('home', compact('userRole', 'roleName', 'user'));
    }
}
