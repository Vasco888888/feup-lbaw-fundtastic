<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    /**
     * Display the forgot password form.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle a forgot password request.
     */
    public function sendResetLinkEmail(Request $request)
    {
        // Validate email
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        // Rate limiting: prevent abuse (1 request per 60 seconds)
        $key = 'password-reset:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            
            throw ValidationException::withMessages([
                'email' => ["Please wait {$seconds} seconds before retrying."],
            ]);
        }

        // Attempt to send the password reset link
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Record the attempt for rate limiting
        RateLimiter::hit($key, 60);

        // Generic message for security (don't reveal if email exists)
        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'If that email address is in our system, we have sent a password reset link.');
        }

        // Still show generic message even on failure
        return back()->with('status', 'If that email address is in our system, we have sent a password reset link.');
    }
}
