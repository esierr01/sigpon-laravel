<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Mostrar una lista de todos los usuarios.
     */
    public function index()
    {
        // Ordenamos: primero activos (desc), luego por nombre (asc)
        $users = User::orderBy('active', 'desc')
            ->orderBy('name', 'asc')
            ->paginate(5);

        return view('usuarios.index', compact('users'));
    }

    /**
     * Mostrar el formulario para crear un nuevo usuario.
     */
    public function create()
    {
        return view('usuarios.create');
    }

    /**
     * Guardar un nuevo usuario en la base de datos.
     */
    public function store(Request $request)
    {
        // Mensajes personalizados en español
        $mensajes = [
            'name.required' => 'El campo nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.unique' => 'Este correo electrónico ya está registrado en el sistema.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.', // El :min se reemplaza automáticamente por el número
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'role.required' => 'Debe seleccionar un rol para el usuario.',
        ];

        // Validación de los datos del formulario (agregamos el arreglo $mensajes como segundo parámetro)
        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|string|email|max:255|unique:users',
            'password'              => 'required|string|min:8|confirmed',
            'role'                  => 'required|integer',
        ], $mensajes);

        // Encriptar la contraseña
        $validated['password'] = Hash::make($validated['password']);

        // Manejo del campo active
        $validated['active'] = $request->has('active');

        // Registrar quién está creando este usuario
        $validated['create_for'] = Auth::id();

        // Crear el usuario
        User::create($validated);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Mostrar los datos de un usuario específico.
     */
    public function show(User $user)
    {
        // Cargamos la relación del usuario que lo creó
        $user->load('creator');

        return view('usuarios.show', compact('user'));
    }

    /**
     * Mostrar el formulario para editar un usuario existente.
     */
    /**
     * Mostrar el formulario para editar un usuario existente.
     */
    public function edit(User $user)
    {
        return view('usuarios.edit', compact('user'));
    }

    /**
     * Actualizar un usuario existente en la base de datos.
     */
    public function update(Request $request, User $user)
    {
        // Validación de los datos
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $user->id, // Ignora el email actual del usuario
            'role'     => 'required|integer',
            'password' => 'nullable|string|min:8', // La contraseña es opcional al editar
        ]);

        // Manejo del campo active (si el checkbox no se marca, no viene en el request)
        $validated['active'] = $request->has('active');

        // Si el campo password viene vacío, lo quitamos del array para que no se sobreescriba en la BD
        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            // Si escribió una nueva contraseña, la encriptamos
            $validated['password'] = Hash::make($validated['password']);
        }

        // Actualizar el usuario
        $user->update($validated);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Conmutar el estado de un usuario (Inhabilitar/Habilitar).
     */
    public function destroy(User $user)
    {
        // Validación: No puedes inhabilitarte a ti mismo
        if ($user->id === Auth::id()) {
            return redirect()->route('usuarios.index')->with('error', 'No puedes inhabilitarte a ti mismo.');
        }

        // Invierte el estado actual: si es true lo pone en false, si es false lo pone en true
        $user->active = !$user->active;
        $user->save();

        // Mensaje dinámico según el nuevo estado
        $estado = $user->active ? 'habilitado' : 'inhabilitado';

        return redirect()->route('usuarios.index')->with('success', "Usuario {$estado} exitosamente.");
    }
}
