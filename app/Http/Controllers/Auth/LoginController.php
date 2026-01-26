<?php
 
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    /**
     * Show the login form.
     *
     * If the user is already authenticated, redirect them
     * to the cards dashboard instead of showing the form.
     */
    public function showLoginForm()
    {
        // Check both default (users) and admin guard
        if (Auth::check() || Auth::guard('admin')->check()) {
            return redirect()->route('landing');
        }

        return view('auth.login');
    }

    /**
     * Process an authentication attempt.
     *
     * Validates the incoming request, checks the provided
     * credentials, and logs the user in if successful.
     * The session is regenerated to protect against session fixation.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        // Validate the request data.
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
 
        // First attempt to authenticate regular users using the default guard.
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('landing'));
        }

        // If user authentication failed, try the admin guard (admins are a separate table).
        if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {
            // Regenerate the session and mark session as admin-authenticated.
            $request->session()->regenerate();
            // Store a small flag so views/middleware can detect an admin session if needed.
            $admin = Auth::guard('admin')->user();
            $request->session()->put('is_admin', true);
            $request->session()->put('admin_id', $admin ? $admin->admin_id : null);

            return redirect()->intended(route('landing'));
        }

        // Authentication failed for both users and admins.
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
}
