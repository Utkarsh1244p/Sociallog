<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';


// Redirect to Provider
Route::get('auth/{provider}', function ($provider) {
    return Socialite::driver($provider)->redirect();
})->name('auth-provider');

// Callback Route
Route::get('auth/{provider}/login', function ($provider) {
    $socialUser = Socialite::driver($provider)->user();
    
    // Check if the user already exists
    $user = User::updateOrCreate([
        'email' => $socialUser->getEmail(),
    ], [
        'name' => $socialUser->getName(),
        'provider_id' => $socialUser->getId(),
        'provider' => $provider,
        'avatar' => $socialUser->getAvatar(),
    ]);

    Auth::login($user);

    return redirect('/dashboard');
});