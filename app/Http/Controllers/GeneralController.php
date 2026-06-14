<?php

namespace App\Http\Controllers;

use App\Models\General;
use App\Models\LogChange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GeneralController extends Controller
{
    public function edit()
    {
        // Buscamos el primer registro. Si no existe, lo creamos vacío (transparente al usuario)
        $general = General::first();
        if (!$general) {
            $general = General::create([
                'rif' => '',
                'department' => '',
                'footer' => ''
            ]);
        }

        return view('general.edit', compact('general'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'rif' => 'required|string|max:20',
            'department' => 'required|string|max:255',
            'title_report_1' => 'nullable|string|max:255',
            'subtitle_report_1' => 'nullable|string|max:255',
            'title_report_2' => 'nullable|string|max:255',
            'subtitle_report_2' => 'nullable|string|max:255',
            'title_report_3' => 'nullable|string|max:255',
            'subtitle_report_3' => 'nullable|string|max:255',
            'title_report_4' => 'nullable|string|max:255',
            'subtitle_report_4' => 'nullable|string|max:255',
            'footer' => 'nullable|string|max:255',
        ]);

        $general = General::first();

        // Si por alguna razón no existe, lo creamos (carga inicial)
        if (!$general) {
            $general = General::create($validated);
            LogChange::create([
                'user_id' => Auth::id(),
                'table' => 'generals',
                'obs' => 'Carga inicial de datos generales del sistema',
                'ip' => $request->ip(),
                'created_at' => now(),
            ]);
            return redirect()->route('general.edit')->with('success', 'Configuración guardada exitosamente.');
        }

        // Si ya existe, actualizamos y logueamos cambios
        $originalData = $general->getOriginal();
        $general->update($validated);

        $cambios = [];
        foreach ($validated as $campo => $nuevoValor) {
            $valorAntiguo = $originalData[$campo] ?? null;
            if ((string)$valorAntiguo !== (string)$nuevoValor) {
                $cambios[] = "{$campo}";
            }
        }

        if (!empty($cambios)) {
            LogChange::create([
                'user_id' => Auth::id(),
                'table' => 'generals',
                'obs' => 'Modificación de datos generales: ' . implode(', ', $cambios),
                'ip' => $request->ip(),
                'created_at' => now(),
            ]);
        }

        return redirect()->route('general.edit')->with('success', 'Configuración actualizada exitosamente.');
    }
}
