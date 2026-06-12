<?php

namespace App\Http\Controllers;

use App\Models\LogChange;
use Illuminate\Http\Request;

class LogChangeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Iniciar la consulta con la relación de usuario precargada
        $query = LogChange::with('user');

        // Aplicar filtro de búsqueda general si se ingresó algo
        if ($request->filled('search')) {
            $search = $request->input('search');
            $searchLower = strtolower($search); // Convertimos a minúsculas

            // Agrupar las condiciones con OR
            $query->where(function ($q) use ($searchLower) {

                // IMPORTANTE: En PostgreSQL, los nombres de columna en consultas crudas deben ir entre comillas dobles ""
                // Especialmente "table" que es una palabra reservada en SQL
                $q->whereRaw('LOWER("table") LIKE ?', ["%{$searchLower}%"])
                    ->orWhereRaw('LOWER("obs") LIKE ?', ["%{$searchLower}%"])

                    // Buscar en el nombre del usuario relacionado (insensible a mayúsculas)
                    ->orWhereHas('user', function ($q2) use ($searchLower) {
                        $q2->whereRaw('LOWER("name") LIKE ?', ["%{$searchLower}%"]);
                    });
            });
        }

        // Ordenar y paginar manteniendo los parámetros en la URL
        $logs = $query->orderBy('created_at', 'desc')
            ->paginate(4)
            ->appends($request->query());

        return view('log-change.index', compact('logs'));
    }
}
