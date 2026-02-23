@extends('layouts.app')

@section('title', 'Bienvenido - Edward Villa Perfumería')

@section('content')
<div class="auth-container-minimal">
    <div class="auth-card-minimal">
        <!-- Header -->
        <div class="auth-header-minimal">
            <h1 class="auth-title-minimal">Bienvenido</h1>
            <p class="auth-subtitle-minimal">Inicia sesión o crea una cuenta</p>
        </div>

        <!-- Tabs -->
        <div class="auth-tabs">
            <button class="auth-tab active" data-tab="login">Iniciar Sesión</button>
            <button class="auth-tab" data-tab="register">Registrarse</button>
        </div>

        <!-- Login Form -->
        <div class="auth-form-container" id="login-form">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="form-group-minimal">
                    <label for="email" class="form-label-minimal">Email</label>
                    <div class="input-wrapper-minimal">
                        <i class="fas fa-envelope input-icon-minimal"></i>
                        <input id="email"
                               type="email"
                               class="form-control-minimal @error('email') is-invalid @enderror"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autocomplete="email"
                               autofocus
                               placeholder="tu@email.com">
                    </div>
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group-minimal">
                    <label for="password" class="form-label-minimal">Contraseña</label>
                    <div class="input-wrapper-minimal">
                        <i class="fas fa-lock input-icon-minimal"></i>
                        <input id="password"
                               type="password"
                               class="form-control-minimal @error('password') is-invalid @enderror"
                               name="password"
                               required
                               autocomplete="current-password"
                               placeholder="••••••••">
                    </div>
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-primary-minimal">
                    Iniciar Sesión
                </button>

                <!-- Divider -->
                <div class="divider-minimal">
                    <span>O continúa con</span>
                </div>

                <!-- Google Button -->
                <a href="{{ route('auth.google') }}" class="btn-google-minimal">
                    <i class="fab fa-google"></i>
                    Continuar con Google
                </a>

                <!-- Forgot Password Link -->
                <div class="auth-links-minimal">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="auth-link-minimal">
                            ¿Olvidaste tu contraseña? <span class="link-highlight">Recupérala aquí</span>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Register Form (Hidden by default) -->
        <div class="auth-form-container d-none" id="register-form">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div class="form-group-minimal">
                    <label for="name" class="form-label-minimal">Nombre Completo</label>
                    <div class="input-wrapper-minimal">
                        <i class="fas fa-user input-icon-minimal"></i>
                        <input id="name"
                               type="text"
                               class="form-control-minimal @error('name') is-invalid @enderror"
                               name="name"
                               value="{{ old('name') }}"
                               required
                               autocomplete="name"
                               placeholder="Tu nombre completo">
                    </div>
                    @error('name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-group-minimal">
                    <label for="register_email" class="form-label-minimal">Email</label>
                    <div class="input-wrapper-minimal">
                        <i class="fas fa-envelope input-icon-minimal"></i>
                        <input id="register_email"
                               type="email"
                               class="form-control-minimal @error('email') is-invalid @enderror"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autocomplete="email"
                               placeholder="tu@email.com">
                    </div>
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group-minimal">
                    <label for="register_password" class="form-label-minimal">Contraseña</label>
                    <div class="input-wrapper-minimal">
                        <i class="fas fa-lock input-icon-minimal"></i>
                        <input id="register_password"
                               type="password"
                               class="form-control-minimal @error('password') is-invalid @enderror"
                               name="password"
                               required
                               autocomplete="new-password"
                               placeholder="••••••••">
                    </div>
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="form-group-minimal">
                    <label for="password_confirmation" class="form-label-minimal">Confirmar Contraseña</label>
                    <div class="input-wrapper-minimal">
                        <i class="fas fa-lock input-icon-minimal"></i>
                        <input id="password_confirmation"
                               type="password"
                               class="form-control-minimal"
                               name="password_confirmation"
                               required
                               autocomplete="new-password"
                               placeholder="••••••••">
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-primary-minimal">
                    Registrarse
                </button>

                <!-- Divider -->
                <div class="divider-minimal">
                    <span>O continúa con</span>
                </div>

                <!-- Google Button -->
                <a href="{{ route('auth.google') }}" class="btn-google-minimal">
                    <i class="fab fa-google"></i>
                    Continuar con Google
                </a>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .auth-container-minimal {
        min-height: calc(100vh - 200px);
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 0rem 0.5rem;
        background-color: #fff;
    }

    .auth-card-minimal {
        width: 100%;
        max-width: 420px;
        background: #ffffff;
        padding: 2.5rem 2rem;
    }

    .auth-header-minimal {
        text-align: center;
        margin-bottom: 2rem;
    }

    .auth-title-minimal {
        font-size: 1.75rem;
        font-weight: 600;
        color: #000;
        margin-bottom: 0.5rem;
        font-family: var(--font-primary);
    }

    .auth-subtitle-minimal {
        font-size: 0.95rem;
        color: #666;
        margin: 0;
        font-weight: 400;
    }

    .auth-tabs {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 2rem;
        background: #f5f5f5;
        border-radius: 8px;
        padding: 4px;
    }

    .auth-tab {
        flex: 1;
        padding: 0.75rem 1rem;
        border: none;
        background: transparent;
        color: #666;
        font-size: 0.9rem;
        font-weight: 500;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .auth-tab.active {
        background: #fff;
        color: #000;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    }

    .form-group-minimal {
        margin-bottom: 1.5rem;
    }

    .form-label-minimal {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: #000;
        margin-bottom: 0.5rem;
    }

    .input-wrapper-minimal {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-icon-minimal {
        position: absolute;
        left: 1rem;
        color: #999;
        font-size: 0.9rem;
        z-index: 1;
    }

    .form-control-minimal {
        width: 100%;
        padding: 0.875rem 1rem 0.875rem 2.75rem;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: #fafafa;
        color: #000;
    }

    .form-control-minimal:focus {
        outline: none;
        border-color: #000;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(0,0,0,0.05);
    }

    .form-control-minimal.is-invalid {
        border-color: #dc3545;
    }

    .error-message {
        color: #dc3545;
        font-size: 0.8rem;
        margin-top: 0.25rem;
    }

    .btn-primary-minimal {
        width: 100%;
        padding: 0.875rem 1rem;
        background: #000;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 0.5rem;
    }

    .btn-primary-minimal:hover {
        background: #222;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .divider-minimal {
        position: relative;
        text-align: center;
        margin: 1.5rem 0;
    }

    .divider-minimal::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background-color: #e0e0e0;
    }

    .divider-minimal span {
        position: relative;
        background-color: #fff;
        padding: 0 1rem;
        color: #999;
        font-size: 0.85rem;
    }

    .btn-google-minimal {
        width: 100%;
        padding: 0.875rem 1rem;
        background: #fff;
        color: #000;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-google-minimal:hover {
        background: #f5f5f5;
        border-color: #ccc;
        text-decoration: none;
        color: #000;
    }

    .btn-google-minimal i {
        font-size: 1.1rem;
    }

    .auth-links-minimal {
        text-align: center;
        margin-top: 1.5rem;
    }

    .auth-link-minimal {
        color: #666;
        font-size: 0.875rem;
        text-decoration: none;
    }

    .auth-link-minimal .link-highlight {
        color: #000;
        font-weight: 600;
    }

    .auth-link-minimal:hover .link-highlight {
        text-decoration: underline;
    }

    @media (max-width: 576px) {
        .auth-card-minimal {
            padding: 2rem 1.5rem;
        }

        .auth-title-minimal {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.auth-tab');
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');

        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const targetTab = this.getAttribute('data-tab');

                // Update active tab
                tabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                // Toggle forms
                if (targetTab === 'login') {
                    loginForm.classList.remove('d-none');
                    registerForm.classList.add('d-none');
                } else {
                    loginForm.classList.add('d-none');
                    registerForm.classList.remove('d-none');
                }
            });
        });

        // If there are register errors, show register form
        @if($errors->has('name') || old('name'))
            document.querySelector('[data-tab="register"]').click();
        @endif
    });
</script>
@endpush
@endsection
