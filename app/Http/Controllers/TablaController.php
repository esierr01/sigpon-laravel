<?php

namespace App\Http\Controllers;

use App\Models\LogChange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TablaController extends Controller
{
    // Mapeo de nombres de tabla a Modelos
    private array $models = [
        'categories' => \App\Models\Category::class,
        'units' => \App\Models\Unit::class,
        'brand_models' => \App\Models\BrandModel::class,
        'suppliers' => \App\Models\Supplier::class,
        'stores' => \App\Models\Store::class,
    ];

    // Nombres legibles en español para los logs
    private array $tableNames = [
        'categories' => 'Categorías',
        'units' => 'Unidades',
        'brand_models' => 'Marcas/Modelos',
        'suppliers' => 'Proveedores',
        'stores' => 'Almacenes',
    ];

    // Reglas de validación por tabla
    private array $rules = [
        'categories' => ['name' => 'required|string|max:100'],
        'units' => ['name' => 'required|string|max:50'],
        'brand_models' => ['brand' => 'required|string|max:100', 'model' => 'required|string|max:100'],
        'suppliers' => ['name' => 'required|string|max:100', 'address' => 'required|string|max:255', 'phone' => 'required|string|max:45', 'contact' => 'required|string|max:100', 'rif' => 'required|string|max:20'],
        'stores' => ['name' => 'required|string|max:100', 'address' => 'required|string|max:255', 'phone' => 'required|string|max:45', 'contact' => 'required|string|max:100'],
    ];

    // Función auxiliar para obtener el nombre del registro según la tabla
    private function getNombreRegistro(string $tabla, $item): string
    {
        if (in_array($tabla, ['categories', 'units', 'suppliers', 'stores'])) {
            return $item->name ?? 'N/A';
        } elseif ($tabla === 'brand_models') {
            return ($item->brand ?? 'N/A') . ' - ' . ($item->model ?? 'N/A');
        }
        return 'N/A';
    }

    public function store(Request $request, string $tabla)
    {
        if (!isset($this->models[$tabla])) abort(404);

        $validated = $request->validate($this->rules[$tabla]);
        $modelClass = $this->models[$tabla];
        $item = $modelClass::create($validated);

        $nombreTabla = $this->tableNames[$tabla] ?? $tabla;
        $nombreRegistro = $this->getNombreRegistro($tabla, $item);

        // Log de carga
        LogChange::create([
            'user_id' => Auth::id(),
            'table' => $nombreTabla,
            'obs' => 'Carga de nuevo registro: ' . $nombreRegistro,
            'ip' => $request->ip(),
            'created_at' => now(),
        ]);

        return redirect()->route('tablas.index', ['tab' => $tabla])->with('success', 'Registro creado exitosamente.');
    }

    public function update(Request $request, string $tabla, int $id)
    {
        if (!isset($this->models[$tabla])) abort(404);

        $validated = $request->validate($this->rules[$tabla]);
        $modelClass = $this->models[$tabla];
        $item = $modelClass::findOrFail($id);

        $originalData = $item->getOriginal();
        $item->update($validated);

        // Construir log de modificación
        $cambios = [];
        foreach ($validated as $campo => $nuevoValor) {
            $valorAntiguo = $originalData[$campo] ?? null;
            if ((string)$valorAntiguo !== (string)$nuevoValor) {
                $cambios[] = "'{$valorAntiguo}' a '{$nuevoValor}'";
            }
        }

        if (!empty($cambios)) {
            $nombreTabla = $this->tableNames[$tabla] ?? $tabla;
            $nombreRegistro = $this->getNombreRegistro($tabla, $item);

            LogChange::create([
                'user_id' => Auth::id(),
                'table' => $nombreTabla,
                'obs' => 'Modificación de ' . implode(', ', $cambios),
                'ip' => $request->ip(),
                'created_at' => now(),
            ]);
        }

        return redirect()->route('tablas.index', ['tab' => $tabla])->with('success', 'Registro actualizado exitosamente.');
    }

    public function destroy(Request $request, string $tabla, int $id)
    {
        if (!isset($this->models[$tabla])) abort(404);

        $modelClass = $this->models[$tabla];
        $item = $modelClass::findOrFail($id);

        // --- VALIDACIÓN DE INTEGRIDAD REFERENCIAL ---
        $inUse = false;
        $mensajeError = '';

        if ($tabla === 'categories' || $tabla === 'units' || $tabla === 'brand_models') {
            $column = $tabla === 'categories' ? 'category_id' : ($tabla === 'units' ? 'unit_id' : 'brand_model_id');
            if (\App\Models\Equipment::where($column, $id)->exists()) {
                $inUse = true;
                $mensajeError = 'No se puede eliminar porque hay Equipos asignados a este registro.';
            }
        } elseif ($tabla === 'suppliers') {
            if (\App\Models\Movement::where('supplier_id', $id)->exists()) {
                $inUse = true;
                $mensajeError = 'No se puede eliminar porque hay Movimientos registrados con este proveedor.';
            }
        } elseif ($tabla === 'stores') {
            if (
                \App\Models\Inventory::where('store_id', $id)->exists() ||
                \App\Models\Movement::where('origin_id', $id)->exists() ||
                \App\Models\Movement::where('destination_id', $id)->exists()
            ) {
                $inUse = true;
                $mensajeError = 'No se puede eliminar porque hay Inventario o Movimientos asociados a este almacén.';
            }
        }

        if ($inUse) {
            return back()->withErrors(['error_general' => $mensajeError])->withInput();
        }
        // --- FIN DE LA VALIDACIÓN ---

        $nombreTabla = $this->tableNames[$tabla] ?? $tabla;
        $nombreRegistro = $this->getNombreRegistro($tabla, $item);

        $item->delete();

        // Log de eliminación con el nombre
        LogChange::create([
            'user_id' => Auth::id(),
            'table' => $nombreTabla,
            'obs' => 'Eliminación de registro: ' . $nombreRegistro,
            'ip' => $request->ip(),
            'created_at' => now(),
        ]);

        return redirect()->route('tablas.index', ['tab' => $tabla])->with('success', 'Registro eliminado exitosamente.');
    }
}
