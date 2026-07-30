<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory;
use App\Models\Movement;
use App\Models\User;
use App\Models\Store;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $userRole = $user->role;

        $roleName = match ($userRole) {
            1 => 'Administrador',
            2 => 'Editor',
            3 => 'Visitante',
            default => 'Sin rol asignado',
        };

        // Métricas reales
        $totalEquiposInventario = Inventory::sum('stock');
        $movimientosMes = Movement::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        $usuariosActivos = User::where('active', true)->count();
        $equiposBajoStock = Inventory::join('equipment', 'inventory.equipment_id', '=', 'equipment.id')
            ->whereColumn('inventory.stock', '<=', 'equipment.umbral')
            ->count();
        $totalMovimientos = Movement::count();
        $almacenesRegistrados = Store::count();

        return view('home', compact(
            'userRole',
            'roleName',
            'user',
            'totalEquiposInventario',
            'movimientosMes',
            'usuariosActivos',
            'equiposBajoStock',
            'totalMovimientos',
            'almacenesRegistrados'
        ));
    }

    public function tablas(Request $request)
    {
        $activeTab = $request->query('tab', 'categories');

        $data = match ($activeTab) {
            'categories' => \App\Models\Category::orderBy('name')->paginate(4),
            'units' => \App\Models\Unit::orderBy('name')->paginate(4),
            'brand_models' => \App\Models\BrandModel::orderBy('brand')->paginate(4),
            'suppliers' => \App\Models\Supplier::orderBy('name')->paginate(4),
            'stores' => \App\Models\Store::orderBy('name')->paginate(4),
            'movement_types' => \App\Models\MovementType::orderBy('id')->get(),
            'roles' => \App\Models\Role::orderBy('id')->get(),
            default => [],
        };

        return view('tablas', compact('activeTab', 'data'));
    }
}
