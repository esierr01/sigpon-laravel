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
        $inventories = Inventory::with(['equipment.category', 'equipment.brandModel', 'equipment.unit', 'store'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Datos para los modales de movimientos
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
            'suppliers'
        ));
    }
}
