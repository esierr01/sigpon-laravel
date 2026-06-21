<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // <-- Importar Storage

class EquipmentController extends Controller
{
    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_model_id' => 'required|exists:brand_models,id',
            'unit_id' => 'required|exists:units,id',
            'umbral' => 'required|integer|min:0',
            'active' => 'required|boolean',
            'img_url_one' => 'nullable|image|max:2048',
            'img_url_two' => 'nullable|image|max:2048',
        ]);

        // 1. Guardamos las rutas de las imágenes viejas ANTES de actualizar
        $oldImg1 = $equipment->img_url_one;
        $oldImg2 = $equipment->img_url_two;

        // 2. Si se subieron imágenes nuevas, las guardamos y actualizamos el array
        if ($request->hasFile('img_url_one')) {
            $validated['img_url_one'] = $request->file('img_url_one')->store('equipments', 'public');
        }
        if ($request->hasFile('img_url_two')) {
            $validated['img_url_two'] = $request->file('img_url_two')->store('equipments', 'public');
        }

        // 3. Actualizamos la base de datos con los datos nuevos
        $equipment->update($validated);

        // 4. Si la BD se actualizó bien, borramos las imágenes viejas del disco
        if ($request->hasFile('img_url_one') && $oldImg1) {
            Storage::disk('public')->delete($oldImg1);
        }
        if ($request->hasFile('img_url_two') && $oldImg2) {
            Storage::disk('public')->delete($oldImg2);
        }

        // Devolvemos JSON porque la petición se hace por AJAX
        return response()->json([
            'success' => true,
            'equipment' => $equipment->load(['unit', 'category', 'brandModel'])
        ]);
    }
}
