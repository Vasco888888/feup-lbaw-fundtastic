@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('password.update') }}">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">

    <h2>Reset Password</h2>
    <p style="margin-bottom: 1.5rem; color: #666;">Enter your email address and choose a new password.</p>

    <label for="email">E-mail</label>
    <input
        id="email"
        name="email"
        type="email"
        value="{{ old('email', $email) }}"
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

    <label for="password">New Password</label>
    <input
        id="password"
        name="password"
        type="password"
        required
        autocomplete="new-password"
        minlength="8"
    >
    @error('password')
        <span id="password-error" class="error" role="alert">
            {{ $message }}
        </span>
    @enderror

    <label for="password_confirmation">Confirm Password</label>
    <input
        id="password_confirmation"
        name="password_confirmation"
        type="password"
        required
        autocomplete="new-password"
        minlength="8"
    >

    <button type="submit">Reset Password</button>
    <a class="button button-outline" href="{{ route('login') }}">Back to Login</a>
</form>
@endsection
