<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\LogAccess;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Sobrescribimos este método para exigir que el usuario esté activo al intentar loguearse.
     * Agregamos la condición 'active' => 1 a las credenciales que Laravel evalúa por defecto.
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request) + ['active' => 1],
            $request->filled('remember')
        );
    }

    /**
     * The user has been authenticated.
     */
    protected function authenticated(Request $request, $user)
    {
        // Actualizar la fecha del último login del usuario
        $user->update(['last_login' => now()]);

        // Registrar intento exitoso
        LogAccess::create([
            'mail' => $request->email,
            'result' => true,
            'obs' => null,
            'created_at' => now(),
        ]);
    }

    /**
     * Get the failed login response instance.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        // Verificar si el usuario existe en la base de datos
        $user = User::where('email', $request->email)->first();

        // Determinar la observación y el mensaje según el caso
        $observacion = '';
        $campoError = 'email'; // Por defecto el error se muestra en el campo email
        $mensajeError = '';

        if (!$user) {
            // El correo no existe
            $observacion = 'Correo no existe';
            $mensajeError = 'Este correo no existe.';
        } elseif (!$user->active) {
            // El usuario existe pero está inactivo
            $observacion = 'Usuario inactivo';
            $mensajeError = 'Usuario inactivo. Contacte al administrador.';
        } else {
            // El usuario existe y está activo, pero la contraseña es incorrecta
            $observacion = 'Contraseña errónea';
            $campoError = 'password'; // Mostramos el error en el campo de contraseña
            $mensajeError = 'Contraseña errónea.';
        }

        // Registrar intento fallido en el log
        LogAccess::create([
            'mail' => $request->email,
            'result' => false,
            'obs' => $observacion,
            'created_at' => now(),
        ]);

        // Lanzar la excepción con el mensaje personalizado
        throw ValidationException::withMessages([
            $campoError => [$mensajeError],
        ]);
    }
}
