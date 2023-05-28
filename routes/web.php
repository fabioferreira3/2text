<?php

use App\Http\Livewire\Dashboard;
use App\Http\Livewire\Blog\BlogPost;
use App\Http\Livewire\NewPost;
use App\Http\Livewire\Templates;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'checktoken'
])->group(function () {
    Route::get('/', function () {
        return redirect('/dashboard');
    });
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/blog/new', NewPost::class)->name('new-post');
    Route::get('/templates', Templates::class)->name('templates');
    Route::get('/documents/blog-post/{document}', BlogPost::class)->name('blog-post');
});

/* Google Auth */

Route::get('/google/auth/redirect', function () {
    return Socialite::driver('google')->redirect();
})->name('login.google');

Route::get('/google/auth/callback', function () {
    $user = Socialite::driver('google')->user();

    $user = User::updateOrCreate([
        'email' => $user->getEmail(),
    ], [
        'name' => $user->getName(),
        'email' => $user->getEmail(),
        'provider' => 'google',
        'provider_id' => $user->getId(),
    ]);
    Auth::login($user);

    return redirect()->route('dashboard');
});
