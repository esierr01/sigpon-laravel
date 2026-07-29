<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Category;
use App\Models\BrandModel;
use App\Models\Unit;
use App\Models\Store;
use App\Models\Supplier;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        // Query base con joins para ordenamiento y relaciones
        $query = Inventory::join('equipment', 'inventory.equipment_id', '=', 'equipment.id')
            ->leftJoin('categories', 'equipment.category_id', '=', 'categories.id')
            ->leftJoin('units', 'equipment.unit_id', '=', 'units.id')
            ->select('inventory.*');

        // 🔍 Filtro de búsqueda (insensible a mayúsculas/minúsculas)
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                // Buscar en nombre del equipo
                $q->whereRaw('LOWER(equipment.name) LIKE ?', ["%{$search}%"])
                    // O en categoría
                    ->orWhereRaw('LOWER(categories.name) LIKE ?', ["%{$search}%"])
                    // O en unidad
                    ->orWhereRaw('LOWER(units.name) LIKE ?', ["%{$search}%"])
                    // O en almacén
                    ->orWhereHas('store', function ($sub) use ($search) {
                        $sub->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                    });
            });
        }

        // Paginación manteniendo orden por nombre de equipo
        $inventories = $query->orderBy('equipment.name', 'asc')
            ->with(['equipment.category', 'equipment.brandModel', 'equipment.unit', 'store'])
            ->paginate(5);

        // Stocks agrupados por equipment_id (solo con stock > 0)
        $stocks = Inventory::with('store:id,name')
            ->where('stock', '>', 0)
            ->get()
            ->groupBy('equipment_id');

        // Datos para modales
        $categories = Category::orderBy('name')->get();
        $brands = BrandModel::orderBy('brand')->get();
        $units = Unit::orderBy('name')->get();
        $stores = Store::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('inventario.index', compact(
            'inventories',
            'categories',
            'brands',
            'units',
            'stores',
            'suppliers',
            'stocks'
        ));
    }
}
