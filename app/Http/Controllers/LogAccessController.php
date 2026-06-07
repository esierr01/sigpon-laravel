<?php

namespace App\Http\Controllers;

use App\Models\LogAccess;
use Illuminate\Http\Request;

class LogAccessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $logs = LogAccess::orderBy('created_at', 'desc')->paginate(5);

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
