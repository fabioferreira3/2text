<?php

use App\Http\Controllers\DocumentViewController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\Blog\BlogPost;
use App\Http\Livewire\Blog\NewPost;
use App\Http\Livewire\Paraphraser\Paraphraser;
use App\Http\Livewire\SocialMediaPost\NewSocialMediaPost;
use App\Http\Livewire\SocialMediaPost\PostsList;
use App\Http\Livewire\Templates;
use App\Http\Livewire\TextTranscription\NewTranscription;
use App\Http\Livewire\TextTranscription\TextTranscription;
use App\Http\Livewire\Trash;
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
    Route::get('/dashboard/trash', Trash::class)->name('trash');
    Route::get('/templates', Templates::class)->name('templates');

    /* Blog routes */
    Route::get('/blog/new', NewPost::class)->name('new-post');
    Route::get('/documents/blog-post/{document}', BlogPost::class)->name('blog-post-view');

    /* Text Transcription routes */
    Route::get('/transcription/new', NewTranscription::class)->name('new-text-transcription');
    Route::get('/documents/transcription/{document}', TextTranscription::class)->name('transcription-view');

    /* Social media posts routes */
    Route::get('/social-media-post/new', NewSocialMediaPost::class)->name('new-social-media-post');
    Route::get('/documents/social-media-post/{document}', PostsList::class)->name('social-media-post-view');

    /* Paraphraser routes */
    Route::get('/paraphraser/new', Paraphraser::class)->name('new-paraphraser');
    //Route::get('/documents/paraphraser/{document}', PostsList::class)->name('social-media-post-view');

    Route::get('/documents/{document}', [DocumentViewController::class, 'index'])->name('document-view');
});

/* Google Auth */

Route::get('/google/auth/redirect', function () {
    return Socialite::driver('google')->redirect();
})->name('login.google');

Route::get('/google/auth/callback', [GoogleAuthController::class, 'handleProviderCallback'])->name('login.google.callback');
