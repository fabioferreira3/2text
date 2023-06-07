<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Repositories\UserRepository;


class GoogleAuthController extends Controller
{
    public function handleProviderCallback(UserRepository $userRepository)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $userParams = [
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'provider' => 'google',
                'provider_id' => $googleUser->getId(),
            ];

            $user = $userRepository->getUserByEmail($googleUser->getEmail());

            if (!$user) {
                $user = $userRepository->registerNewUserFromProvider($userParams);
            } else {
                $user->update($userParams);
            }

            Auth::login($user);

            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            // Handle the error properly here. Maybe log it and redirect with an error message.
            return redirect()->route('login')->with('error', 'Unable to login with Google. Please try again.');
        }
    }
}
