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

    public function tablas(Request $request)
    {
        $activeTab = $request->query('tab', 'categories');

        // Cargar los datos según la pestaña activa
        $data = match ($activeTab) {
            'categories' => \App\Models\Category::orderBy('name')->paginate(4),
            'units' => \App\Models\Unit::orderBy('name')->paginate(4),
            'brand_models' => \App\Models\BrandModel::orderBy('brand')->paginate(4),
            'suppliers' => \App\Models\Supplier::orderBy('name')->paginate(4),
            'stores' => \App\Models\Store::orderBy('name')->paginate(4),
            default => [],
        };

        return view('tablas', compact('activeTab', 'data'));
    }
}
