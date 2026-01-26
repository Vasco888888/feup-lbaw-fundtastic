<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Illuminate\View\View;

use App\Models\User;

class RegisterController extends Controller
{
    /**
     * Show the user registration form.
     */
    public function showRegistrationForm(): View
    {
        // Render the registration view.
        return view('auth.register');
    }

    /**
     * Handle a new user registration request.
     *
     * This method:
     * - Validates the registration input data.
     * - Creates a new user with a hashed password.
     * - Logs the user in automatically after registration.
     * - Regenerates the session to prevent fixation attacks.
     * - Redirects the user to the cards page with a success message.
     */
    public function register(Request $request)
    {
        // Validate registration input. Do a model-based unique check because
        // this project uses a schema-qualified table name (not the default "users").
        $request->validate([
            'name' => 'required|string|max:250',
            'email' => ['required','email','max:250', function ($attribute, $value, $fail) {
                if (User::where('email', $value)->exists()) {
                    $fail('The ' . $attribute . ' has already been taken.');
                    return;
                }
                // Also prevent registering with an email that already belongs to an admin.
                if (class_exists(\App\Models\Admin::class) && \App\Models\Admin::where('email', $value)->exists()) {
                    $fail('The ' . $attribute . ' has already been taken.');
                }
            }],
            'password' => 'required|min:8|confirmed'
        ]);

        // Create the new user.
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        // Attempt login for the newly registered user.
        $credentials = $request->only('email', 'password');
        Auth::attempt($credentials);

        // Regenerate session for security (protection against session fixation).
        $request->session()->regenerate();

        // Redirect to campaigns index with a success message.
        return redirect()->route('campaigns.index')
            ->withSuccess('You have successfully registered & logged in!');
    }
}
