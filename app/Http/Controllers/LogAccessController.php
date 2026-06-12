<?php

namespace App\Http\Controllers;

use App\Models\LogAccess;
use Illuminate\Http\Request;

class LogAccessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Iniciar la consulta
        $query = LogAccess::query();

        // Aplicar filtro de búsqueda general si se ingresó algo
        if ($request->filled('search')) {
            $search = $request->input('search');
            $searchLower = strtolower($search); // Convertimos a minúsculas

            // Mapeo de palabras clave a valores booleanos de la base de datos
            $resultSearch = null;
            if (stripos($search, 'exitoso') !== false || stripos($search, 'exito') !== false) {
                $resultSearch = 1; // True en BD
            } elseif (stripos($search, 'fallido') !== false || stripos($search, 'fallo') !== false) {
                $resultSearch = 0; // False en BD
            }

            // Agrupar las condiciones con OR
            $query->where(function ($q) use ($searchLower, $resultSearch) {

                // Usamos whereRaw con LOWER() para ignorar mayúsculas/minúsculas en la BD
                $q->whereRaw('LOWER(mail) LIKE ?', ["%{$searchLower}%"])
                    ->orWhereRaw('LOWER(obs) LIKE ?', ["%{$searchLower}%"]);

                // Si la palabra escrita coincide con un resultado, lo agrega a la búsqueda
                if ($resultSearch !== null) {
                    $q->orWhere('result', $resultSearch);
                }
            });
        }

        // Ordenar y paginar manteniendo los parámetros en la URL
        $logs = $query->orderBy('created_at', 'desc')
            ->paginate(5)
            ->appends($request->query());

        return view('log-access.index', compact('logs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mail' => 'required|email|max:100',
            'result' => 'required|boolean',
            'obs' => 'nullable|string|max:255',
        ]);

        $logAccess = LogAccess::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Registro de acceso guardado correctamente',
            'data' => $logAccess
        ], 201);
    }
}
