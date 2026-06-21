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

        // Construir log detallado de modificaciones
        // 1. Mapeo de nombres técnicos a nombres legibles
        $etiquetas = [
            'rif' => 'RIF',
            'department' => 'Dpto.',
            'footer' => 'Pie/Página',
            'title_report_1' => 'Tít Rep (Stock por Almacén)',
            'subtitle_report_1' => 'Subtít Rep (Stock por Almacén)',
            'title_report_2' => 'Tít Rep (Stock Mínimo)',
            'subtitle_report_2' => 'Subtít Rep (Stock Mínimo)',
            'title_report_3' => 'Tít Rep (Movimientos)',
            'subtitle_report_3' => 'Subtít Rep (Movimientos)',
            'title_report_4' => 'Tít Rep (Historial)',
            'subtitle_report_4' => 'Subtít Rep (Historial)',
        ];

        $cambios = [];
        foreach ($validated as $campo => $nuevoValor) {
            $valorAntiguo = $originalData[$campo] ?? '';

            if ((string)$valorAntiguo !== (string)$nuevoValor) {
                // 2. Usar la etiqueta legible, o el nombre del campo si no está en el mapeo
                $nombreCampo = $etiquetas[$campo] ?? $campo;

                // 3. Si el valor antiguo era vacío, decimos que es un "Carga inicial del campo"
                if (empty($valorAntiguo)) {
                    $cambios[] = "{$nombreCampo} establecido a '{$nuevoValor}'";
                } else {
                    $cambios[] = "{$nombreCampo} de '{$valorAntiguo}' a '{$nuevoValor}'";
                }
            }
        }

        if (!empty($cambios)) {
            $obsLog = 'Modif.: ' . implode(', ', $cambios);

            // Si el texto supera los 255 caracteres, lo truncamos a 252 y agregamos "..."
            if (mb_strlen($obsLog) > 255) {
                $obsLog = mb_substr($obsLog, 0, 252) . '...';
            }

            LogChange::create([
                'user_id' => Auth::id(),
                'table' => 'General',
                'obs' => $obsLog,
                'ip' => $request->ip(),
                'created_at' => now(),
            ]);
        }

        return redirect()->route('general.edit')->with('success', 'Configuración actualizada exitosamente.');
    }
}
