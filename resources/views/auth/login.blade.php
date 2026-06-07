@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center mt-3">
            <div class="col-md-6 d-flex align-items-center justify-content-center">
                <img src="{{ asset('image/logosigpon.png') }}" class="img-fluid mx-2 rounded-end-5 rounded-start-5"
                    alt="Logo">
            </div>
            <div class="col-md-6 mt-4">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary font-bold text-black text-center">{{ __('Acceso al Sistema') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="row mt-4 mb-4">
                                <label for="email"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Correo') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email') }}" placeholder="Ingrese correo autorizado" required
                                        autocomplete="email" maxlength="100" autofocus>

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Contraseña') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" placeholder="Ingrese contraseña"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required autocomplete="current-password" maxlength="155">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mt-5 mb-2">
                                <div class="col-md-6 offset-md-3">
                                    <button type="submit" class="btn btn-primary w-100">
                                        {{ __('Login') }}
                                    </button>

                                    @if (Route::has('password.request'))
                                        <a class="btn btn-link d-block mt-2" href="{{ route('password.request') }}">
                                            {{ __('Olvidaste tu contraseña?') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <p class="text-secondary text-center mt-2">* Contacte al administrador del sistema para obtener acceso.</p>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    <footer class="footer text-center mt-5">
        <p class="text-muted fw-lighter text-sm-center small">
            &copy;
            2026
            -
            SIGPON
            -
            Desarrollado por Emmanuel Sierra
        </p>
    </footer>
@endsection
