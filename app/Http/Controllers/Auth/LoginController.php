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
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        // Verificar si el usuario existe en la base de datos
        $user = User::where('email', $request->email)->first();

        // Determinar la observación según el caso
        $observacion = '';

        if (!$user) {
            // El correo no existe
            $observacion = 'Correo no existe';
        } else {
            // El correo existe pero la contraseña es incorrecta
            $observacion = 'Contraseña errónea';
        }
        // Registrar intento fallido
        LogAccess::create([
            'mail' => $request->email,
            'result' => false,
            'obs' => $observacion,
            'created_at' => now(),
        ]);
        if (!$user) {
            // El correo no existe
            throw ValidationException::withMessages([
                'email' => ['Este correo no existe.'],
            ]);
        } else {
            // El correo existe pero la contraseña es incorrecta
            throw ValidationException::withMessages([
                'password' => ['Contraseña errónea.'],
            ]);
        }
    }
}
