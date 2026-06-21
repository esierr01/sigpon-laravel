<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LogChange; // <-- 1. IMPORTAR EL MODELO
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Mostrar una lista de todos los usuarios.
     */
    /**
     * Mostrar una lista de todos los usuarios.
     */
    /**
     * Mostrar una lista de todos los usuarios.
     */
    public function index(Request $request)
    {
        // Iniciar la consulta
        $query = User::query();

        // Aplicar filtro de búsqueda general si se ingresó algo
        if ($request->filled('search')) {
            $search = $request->input('search');
            $searchLower = strtolower($search); // Convertimos a minúsculas

            // Mapeo de palabras clave a valores de la base de datos (stripos ya es insensible a mayúsculas)
            $roleSearch = null;
            if (stripos($search, 'admin') !== false) $roleSearch = 1;
            elseif (stripos($search, 'editor') !== false) $roleSearch = 2;
            elseif (stripos($search, 'visitante') !== false) $roleSearch = 3;

            $statusSearch = null;
            if (stripos($search, 'activo') !== false) $statusSearch = 1;
            if (stripos($search, 'inactivo') !== false) $statusSearch = 0;

            // Agrupar las condiciones con OR
            $query->where(function ($q) use ($searchLower, $roleSearch, $statusSearch) {

                // Usamos whereRaw con LOWER() para ignorar mayúsculas/minúsculas en la BD
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$searchLower}%"]);

                // Si la palabra escrita coincide con un rol, lo agrega a la búsqueda
                if ($roleSearch !== null) {
                    $q->orWhere('role', $roleSearch);
                }

                // Si la palabra escrita coincide con un estado, lo agrega a la búsqueda
                if ($statusSearch !== null) {
                    $q->orWhere('active', $statusSearch);
                }
            });
        }

        // Ordenar y paginar manteniendo los parámetros en la URL
        $users = $query->orderBy('active', 'desc')
            ->orderBy('name', 'asc')
            ->paginate(5)
            ->appends($request->query());

        return view('usuarios.index', compact('users'));
    }


    public function create()
    {
        return view('usuarios.create');
    }

    /**
     * Guardar un nuevo usuario en la base de datos.
     */
    public function store(Request $request)
    {
        $mensajes = [
            'name.required' => 'El campo nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.unique' => 'Este correo electrónico ya está registrado en el sistema.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'role.required' => 'Debe seleccionar un rol para el usuario.',
        ];

        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|string|email|max:255|unique:users',
            'password'              => 'required|string|min:8|confirmed',
            'role'                  => 'required|integer',
        ], $mensajes);

        $validated['password'] = Hash::make($validated['password']);
        $validated['active'] = $request->has('active');
        $validated['create_for'] = Auth::id();

        $user = User::create($validated); // Guardamos el usuario en una variable

        // <-- 2. REGISTRAR LOG DE CARGA
        LogChange::create([
            'user_id' => Auth::id(),
            'table' => 'Usuarios',
            'obs' => 'Carga de nuevo usuario: ' . $user->email,
            'ip' => $request->ip(),
            'created_at' => now(),
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function show(User $user)
    {
        $user->load('creator');
        return view('usuarios.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('usuarios.edit', compact('user'));
    }

    /**
     * Actualizar un usuario existente en la base de datos.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role'     => 'required|integer',
            'password' => 'nullable|string|min:8',
        ]);

        $validated['active'] = $request->has('active');

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        // <-- 3. CAPTURAR DATOS ORIGINALES ANTES DE ACTUALIZAR
        $originalData = $user->getOriginal();

        $user->update($validated);

        // <-- 4. CONSTRUIR LA OBSERVACIÓN Y REGISTRAR LOG DE MODIFICACIÓN
        $cambios = [];
        $roles = [1 => 'Admin', 2 => 'Editor', 3 => 'Visitante'];

        foreach ($validated as $campo => $nuevoValor) {
            $valorAntiguo = $originalData[$campo] ?? null;

            // No logeamos la contraseña real por seguridad, solo si se cambió
            if ($campo === 'password') {
                if (!empty($nuevoValor)) {
                    $cambios[] = "Contraseña actualizada";
                }
                continue;
            }

            // Formatear el campo 'active' para que sea legible
            if ($campo === 'active') {
                $valorAntiguo = $valorAntiguo ? 'Activo' : 'Inactivo';
                $nuevoValor = $nuevoValor ? 'Activo' : 'Inactivo';
            }

            // Formatear el campo 'role' para que diga el nombre, no el número
            if ($campo === 'role') {
                $valorAntiguo = $roles[$valorAntiguo] ?? $valorAntiguo;
                $nuevoValor = $roles[$nuevoValor] ?? $nuevoValor;
            }

            // Comparar si el valor realmente cambió
            if ((string)$valorAntiguo !== (string)$nuevoValor) {
                $cambios[] = "{$campo}: antes '{$valorAntiguo}' - ahora '{$nuevoValor}'";
            }
        }

        // Solo guardar el log si hubo cambios reales
        if (!empty($cambios)) {
            LogChange::create([
                'user_id' => Auth::id(),
                'table' => 'Usuarios',
                'obs' => 'Modificación: ' . $user->email . ' | ' . implode(', ', $cambios),
                'ip' => $request->ip(),
                'created_at' => now(),
            ]);
        }

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Conmutar el estado de un usuario (Inhabilitar/Habilitar).
     */
    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('usuarios.index')->with('error', 'No puedes inhabilitarte a ti mismo.');
        }

        $user->active = !$user->active;
        $user->save();

        $estado = $user->active ? 'habilitado' : 'inhabilitado';
        $accion = $user->active ? 'Habilitación' : 'Inhabilitación';

        // <-- 5. REGISTRAR LOG DE HABILITAR/INHABILITAR
        LogChange::create([
            'user_id' => Auth::id(),
            'table' => 'Usuarios',
            'obs' => "{$accion} de usuario: {$user->email}",
            'ip' => request()->ip(), // Aquí usamos el helper global de Laravel
            'created_at' => now(),
        ]);

        return redirect()->route('usuarios.index')->with('success', "Usuario {$estado} exitosamente.");
    }

    /**
     * Mostrar el formulario para cambiar la contraseña del usuario actual.
     */
    public function editPassword()
    {
        return view('usuarios.password');
    }

    /**
     * Actualizar la contraseña del usuario actual.
     */
    public function updatePassword(Request $request)
    {
        $mensajes = [
            'current_password.required' => 'Debe ingresar su contraseña actual.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La nueva contraseña debe tener al menos :min caracteres.',
            'password.confirmed' => 'Las nuevas contraseñas no coinciden.',
        ];

        $validated = $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) {
                // Validar que la contraseña actual ingresada sea correcta
                if (!Hash::check($value, Auth::user()->password)) {
                    $fail('La contraseña actual es incorrecta.');
                }
            }],
            'password' => 'required|string|min:8|confirmed',
        ], $mensajes);

        // Obtener el usuario directamente del Modelo usando el ID de la sesión
        $user = User::find(Auth::id());

        // Actualizar y guardar
        $user->password = Hash::make($validated['password']);
        $user->save();

        // Registrar en el log de cambios
        LogChange::create([
            'user_id' => Auth::id(),
            'table' => 'Usuarios',
            'obs' => 'Cambio de contraseña propio',
            'ip' => $request->ip(),
            'created_at' => now(),
        ]);

        // Cambiar 'password.change' por 'profile.password'
        return redirect()->route('profile.password')->with('success', 'Contraseña actualizada exitosamente.');
    }
}
