<?php

use App\Http\Controllers\DocumentViewController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\Blog\BlogPost;
use App\Http\Livewire\Blog\NewPost;
use App\Http\Livewire\Paraphraser\NewParaphraser;
use App\Http\Livewire\Paraphraser\Paraphraser;
use App\Http\Livewire\SocialMediaPost\NewSocialMediaPost;
use App\Http\Livewire\SocialMediaPost\SocialMediaPostsManager;
use App\Http\Livewire\Templates;
use App\Http\Livewire\TextToSpeech\History;
use App\Http\Livewire\TextToSpeech\TextToAudio;
use App\Http\Livewire\TextTranscription\NewTranscription;
use App\Http\Livewire\TextTranscription\TextTranscription;
use App\Http\Livewire\Trash;
use App\Models\Voice;
use Talendor\ElevenLabsClient\ElevenLabsClient;
use Talendor\ElevenLabsClient\TextToSpeech\TextToSpeech;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
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
    'checktoken',
    'maintenance'
])->group(function () {
    Route::get('/', function () {
        return redirect('/tools');
    });
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/dashboard/trash', Trash::class)->name('trash');
    Route::get('/tools', Templates::class)->name('tools');

    /* Blog routes */
    Route::get('/blog/new', NewPost::class)->name('new-post');
    Route::get('/documents/blog-post/{document}', BlogPost::class)->name('blog-post-view');

    /* Text Transcription routes */
    Route::get('/transcription/new', NewTranscription::class)->name('new-text-transcription');
    Route::get('/documents/transcription/{document}', TextTranscription::class)->name('transcription-view');

    /* Social media posts routes */
    Route::get('/social-media-post/new', NewSocialMediaPost::class)->name('new-social-media-post');
    Route::get('/documents/social-media/{document}', SocialMediaPostsManager::class)->name('social-media-view');

    /* Paraphraser routes */
    Route::get('/paraphraser/new', NewParaphraser::class)->name('new-paraphraser');
    Route::get('/documents/paraphraser/{document}', Paraphraser::class)->name('paraphrase-view');

    /* Text to Speech routes */
    Route::get('/text-to-speech/new', TextToAudio::class)->name('new-text-to-speech');
    Route::get('/text-to-speech/history', History::class)->name('text-to-speech-history');
    Route::get('/documents/text-to-speech/{document}', TextToAudio::class)->name('text-to-speech-view');

    Route::get('/documents/{document}', [DocumentViewController::class, 'index'])->name('document-view');


    Route::get('/elevenlabs', function () {
        //  Voice::truncate();
        $elevenLabsClient = app()->make(ElevenLabsClient::class);
        $voices = collect($elevenLabsClient->voices()->getAll());
        // $voices->each(function ($voice) {
        //     $model = null;
        //     if (count($voice['high_quality_base_model_ids'])) {
        //         $model = $voice['high_quality_base_model_ids'][0];
        //     }
        //     Voice::create([
        //         'external_id' => $voice['voice_id'],
        //         'name' => $voice['name'],
        //         'preview_url' => $voice['preview_url'],
        //         'model' => $model,
        //         'provider' => 'elevenlabs',
        //         'meta' => $voice['labels']
        //     ]);
        // });
        return $voices;
    });
});

/* Google Auth */

Route::get('/google/auth/redirect', function () {
    return Socialite::driver('google')->redirect();
})->name('login.google');

Route::get('/google/auth/callback', [GoogleAuthController::class, 'handleProviderCallback'])->name('login.google.callback');

/* Stripe Webhook */
Route::post('/stripe/payment', [GoogleAuthController::class, 'handleProviderCallback'])->name('payment.webhook');
