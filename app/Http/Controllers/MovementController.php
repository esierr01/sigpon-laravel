<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Inventory;
use App\Models\Movement;
use App\Models\LogChange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MovementController extends Controller
{
    public function index()
    {
        $movements = Movement::with(['equipment', 'supplier', 'origin', 'destination', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('movimientos.index', compact('movements'));
    }

    public function store(Request $request)
    {
        $rules = [
            'movement_type' => 'required|integer|in:1,2,3,4',
            'amount'        => 'required|integer|min:1',
            'obs'           => 'nullable|string|max:255',
        ];

        // Mensajes personalizados en español
        $mensajes = [
            'new_sku.required_without' => 'El SKU es obligatorio si está creando un equipo nuevo.',
            'new_name.required_without' => 'El nombre es obligatorio si está creando un equipo nuevo.',
            'category_id.required_without' => 'La categoría es obligatoria si está creando un equipo nuevo.',
            'brand_model_id.required_without' => 'La marca/modelo es obligatorio si está creando un equipo nuevo.',
            'unit_id.required_without' => 'La unidad es obligatoria si está creando un equipo nuevo.',
            'umbral.required_without' => 'El umbral mínimo es obligatorio si está creando un equipo nuevo.',

            'category_id.exists' => 'La categoría seleccionada no es válida.',
            'brand_model_id.exists' => 'La marca/modelo seleccionado no es válido.',
            'unit_id.exists' => 'La unidad seleccionada no es válida.',

            'img_url_one.image' => 'La Imagen 1 debe ser un archivo de imagen válido.',
            'img_url_one.max' => 'La Imagen 1 no debe pesar más de 2MB.',
            'img_url_two.image' => 'La Imagen 2 debe ser un archivo de imagen válido.',
            'img_url_two.max' => 'La Imagen 2 no debe pesar más de 2MB.',

            'destination_id.required' => 'Debe seleccionar un almacén de destino.',
            'destination_id.exists' => 'El almacén de destino no es válido.',
            'origin_id.required' => 'Debe seleccionar un almacén de origen.',
            'origin_id.exists' => 'El almacén de origen no es válido.',
            'supplier_id.required' => 'Debe seleccionar un proveedor.',
            'supplier_id.exists' => 'El proveedor seleccionado no es válido.',
            'amount.required' => 'La cantidad es obligatoria.',
            'amount.min' => 'La cantidad debe ser al menos 1.',
            'amount.integer' => 'La cantidad debe ser un número entero.',
        ];

        // Reglas dinámicas según el tipo de movimiento
        if ($request->movement_type == 1) { // Compra
            $rules['equipment_id'] = 'nullable|exists:equipment,id';
            $rules['destination_id'] = 'required|exists:stores,id';
            $rules['supplier_id'] = 'required|exists:suppliers,id';
            $rules['new_sku'] = 'nullable|required_without:equipment_id|string|max:50';
            $rules['new_name'] = 'nullable|required_without:equipment_id|string|max:255';
            $rules['category_id'] = 'nullable|required_without:equipment_id|exists:categories,id';
            $rules['brand_model_id'] = 'nullable|required_without:equipment_id|exists:brand_models,id';
            $rules['unit_id'] = 'nullable|required_without:equipment_id|exists:units,id';
            $rules['umbral'] = 'nullable|required_without:equipment_id|integer|min:0';
            $rules['img_url_one'] = 'nullable|image|max:2048';
            $rules['img_url_two'] = 'nullable|image|max:2048';
        } elseif ($request->movement_type == 2) { // Salida
            $rules['equipment_id'] = 'required|exists:equipment,id';
            $rules['origin_id'] = 'required|exists:stores,id';
        } elseif ($request->movement_type == 3) { // Traslado
            $rules['equipment_id'] = 'required|exists:equipment,id';
            $rules['origin_id'] = 'required|exists:stores,id';
            $rules['destination_id'] = 'required|exists:stores,id|different:origin_id';
            $mensajes['destination_id.different'] = 'El almacén de destino debe ser diferente al de origen.';
        } elseif ($request->movement_type == 4) { // Ajuste
            $rules['equipment_id'] = 'required|exists:equipment,id';
            $rules['origin_id'] = 'required|exists:stores,id';
            $rules['amount'] = 'required|integer';
        }

        // Pasamos las reglas y los mensajes a la validación
        $validated = $request->validate($rules, $mensajes);

        try {
            DB::transaction(function () use ($validated, $request) {
                $equipmentId = $validated['equipment_id'] ?? null;
                $amount = $validated['amount'];
                $userId = Auth::id();

                // 1) COMPRA
                if ($validated['movement_type'] == 1) {
                    if (!$equipmentId) {
                        $equipData = [
                            'sku' => $validated['new_sku'],
                            'name' => $validated['new_name'],
                            'category_id' => $validated['category_id'],
                            'brand_model_id' => $validated['brand_model_id'],
                            'unit_id' => $validated['unit_id'],
                            'umbral' => $validated['umbral'] ?? 0,
                            'user_id' => $userId,
                            'active' => true,
                        ];

                        if ($request->hasFile('img_url_one')) {
                            $equipData['img_url_one'] = $request->file('img_url_one')->store('equipments', 'public');
                        }
                        if ($request->hasFile('img_url_two')) {
                            $equipData['img_url_two'] = $request->file('img_url_two')->store('equipments', 'public');
                        }

                        $equip = Equipment::create($equipData);
                        $equipmentId = $equip->id;
                    }

                    $inventory = Inventory::firstOrCreate(
                        ['equipment_id' => $equipmentId, 'store_id' => $validated['destination_id']],
                        ['stock' => 0, 'user_id' => $userId]
                    );
                    $inventory->stock += $amount;
                    $inventory->last_change = now();
                    $inventory->save();
                }
                // 2, 3, 4) SALIDA, TRASLADO, AJUSTE
                else {
                    $inventory = Inventory::where('equipment_id', $equipmentId)
                        ->where('store_id', $validated['origin_id'])
                        ->firstOrFail();

                    if ($validated['movement_type'] == 2) {
                        if ($inventory->stock < $amount) {
                            throw new \Exception("Stock insuficiente. Disponible: {$inventory->stock}");
                        }
                        $inventory->stock -= $amount;
                    } elseif ($validated['movement_type'] == 3) {
                        if ($inventory->stock < $amount) {
                            throw new \Exception("Stock insuficiente para traslado. Disponible: {$inventory->stock}");
                        }
                        $inventory->stock -= $amount;

                        $destInventory = Inventory::firstOrCreate(
                            ['equipment_id' => $equipmentId, 'store_id' => $validated['destination_id']],
                            ['stock' => 0, 'user_id' => $userId]
                        );
                        $destInventory->stock += $amount;
                        $destInventory->last_change = now();
                        $destInventory->save();
                    } elseif ($validated['movement_type'] == 4) {
                        $inventory->stock += $amount;
                        if ($inventory->stock < 0) $inventory->stock = 0;
                    }

                    $inventory->last_change = now();
                    $inventory->save();
                }

                // Guardar el movimiento historial
                Movement::create([
                    'movement_type' => $validated['movement_type'],
                    'equipment_id' => $equipmentId,
                    'supplier_id' => $validated['supplier_id'] ?? null,
                    'origin_id' => $validated['origin_id'] ?? null,
                    'destination_id' => $validated['destination_id'] ?? null,
                    'amount' => $amount,
                    'obs' => $validated['obs'] ?? null,
                    'user_id' => $userId,
                ]);

                // Log de cambio
                LogChange::create([
                    'user_id' => $userId,
                    'table' => 'movements',
                    'obs' => 'Nuevo movimiento tipo ' . $validated['movement_type'] . ' - Equipo ID: ' . $equipmentId,
                    'ip' => $request->ip(),
                    'created_at' => now(),
                ]);
            });

            return redirect()->route('inventory.index')->with('success', 'Movimiento procesado exitosamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error_general' => $e->getMessage()])->withInput();
        }
    }

    public function show(Movement $movement)
    {
        $movement->load(['equipment', 'supplier', 'origin', 'destination', 'user']);
        return response()->json($movement);
    }
}
