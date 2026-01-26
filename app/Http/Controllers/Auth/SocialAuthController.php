<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function callbackGoogle()
    {
        $google_user = Socialite::driver('google')->stateless()->user();
        $user = User::where('google_id', $google_user->getId())->first();

        // If the user does not exist, create one
        if (!$user) {
            $new_user = User::create([
                'name' => $google_user->getName(),
                'email' => $google_user->getEmail(),
                'google_id' => $google_user->getId(),
                'external_profile_image' => $google_user->getAvatar(),
            ]);

            Auth::login($new_user, true);

            // After login, redirect to homepage
            return redirect()->intended(route('campaigns.index'));
        } else {
            // Otherwise, simply log in with the existing user
            // Update profile image if available
            if ($google_user->getAvatar() && $user->external_profile_image !== $google_user->getAvatar()) {
                $user->external_profile_image = $google_user->getAvatar();
                $user->save();
            }
            
            Auth::login($user, true);

            return redirect()->intended(route('campaigns.index'));
        }
    }
}
