@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <h2>Forgot Password</h2>
    <p style="margin-bottom: 1.5rem; color: #666;">Enter your email address and we'll send you a link to reset your password.</p>

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
        placeholder="your@email.com"
    >
    @error('email')
        <span id="email-error" class="error" role="alert">
            {{ $message }}
        </span>
    @enderror

    <button type="submit">Send Reset Link</button>
    <a class="button button-outline" href="{{ route('login') }}">Back to Login</a>

    @if (session('status'))
        <div style="margin-top: 1.5rem; padding: 12px; background: #e6f7ff; border: 1px solid #91d5ff; border-radius: 4px; color: #0050b3; text-align: center;">
            {{ session('status') }}
        </div>
    @endif
</form>
@endsection
