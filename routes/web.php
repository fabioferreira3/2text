<?php

use App\Http\Livewire\Dashboard;
use App\Http\Livewire\DocumentView;
use App\Http\Livewire\MyDocuments;
use App\Http\Livewire\NewPost;
use App\Http\Livewire\PendingJobs;
use App\Http\Livewire\Templates;
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
    'verified'
])->group(function () {
    Route::get('/', function () {
        return redirect('/dashboard');
    });
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/blog/new', NewPost::class)->name('new-post');
    Route::get('/templates', Templates::class)->name('templates');
    Route::get('/documents/{document}', DocumentView::class)->name('document-view');
});

Route::get('/google/auth/redirect', function () {
    return Socialite::driver('google')->redirect();
})->name('login.google');

Route::get('/linkedin/auth/redirect', function () {
    return Socialite::driver('linkedin')->redirect();
})->name('login.linkedin');

Route::get('/medium/auth/redirect', function () {
    return Socialite::driver('medium')->redirect();
})->name('login.medium');

Route::get('/google/auth/callback', function () {
    $user = Socialite::driver('google')->user();

    // $user->token
});
