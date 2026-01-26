@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('login') }}">
    @csrf

    <label for="email">E-mail</label>
    <input
        id="email"
        name="email"
        type="email"
        value="{{ old('email') }}"
        required
        autofocus
        inputmode="email"
        autocomplete="email"
    >
    @error('email')
        <span id="email-error" class="error" role="alert">
            {{ $message }}
        </span>
    @enderror

    <label for="password" >Password</label>
    <input
        id="password"
        name="password"
        type="password"
        required
        autocomplete="current-password"
    >
    @error('password')
        <span id="password-error" class="error" role="alert">
            {{ $message }}
        </span>
    @enderror

    {{-- 
        TODO: not in db
        
    <label>
        <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
        Remember me
    </label>
    --}}

    <button type="submit">Login</button>
    <a class="button button-outline" href="{{ route('register') }}">Register</a>

    <div class="oauth-signin">
        <a class="button button-google" href="{{ route('google-auth') }}">
            <img class="google-logo" src="{{ asset('images/googlelogo.png') }}" alt="Google logo" width="18" height="18" onerror="this.onerror=null;this.src='{{ asset('images/google-logo.svg') }}'" />
            <span class="google-text">Sign in with Google</span>
        </a>
    </div>

    <div style="text-align: center; margin-top: 1rem;">
        <a href="{{ route('password.request') }}" style="color: #4A90E2; text-decoration: none; font-size: 0.9rem;">
            Forgot your password?
        </a>
    </div>

    @if (session('status'))
        <div style="margin-top: 1.5rem; padding: 12px; background: #e6f7ff; border: 1px solid #91d5ff; border-radius: 4px; color: #0050b3; text-align: center;">
            {{ session('status') }}
        </div>
    @endif
</form>
@endsection