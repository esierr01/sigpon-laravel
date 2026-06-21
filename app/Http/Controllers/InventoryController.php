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
    public function index()
    {
        // Usamos join para poder ordenar por el nombre del equipo
        $inventories = Inventory::join('equipment', 'inventory.equipment_id', '=', 'equipment.id')
            ->orderBy('equipment.name', 'asc')
            ->select('inventory.*') // Corregido a 'inventory'
            ->with(['equipment.category', 'equipment.brandModel', 'equipment.unit', 'store'])
            ->paginate(5);

        // Obtenemos los inventarios con stock > 0 y los agrupamos por equipment_id
        $stocks = Inventory::with('store:id,name')
            ->where('stock', '>', 0)
            ->get()
            ->groupBy('equipment_id');

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
