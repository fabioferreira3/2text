<?php

use App\Http\Controllers\DocumentViewController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\Blog\BlogPost;
use App\Http\Livewire\Blog\NewPost;
use App\Http\Livewire\Blog\ProcessingBlogPost;
use App\Http\Livewire\Paraphraser\NewParaphraser;
use App\Http\Livewire\Paraphraser\Paraphraser;
use App\Http\Livewire\SocialMediaPost\NewSocialMediaPost;
use App\Http\Livewire\SocialMediaPost\SocialMediaPostsManager;
use App\Http\Livewire\SocialMediaPost\TempNew;
use App\Http\Livewire\Templates;
use App\Http\Livewire\TextToSpeech\AudioHistory;
use App\Http\Livewire\TextToSpeech\TextToAudio;
use App\Http\Livewire\AudioTranscription\NewTranscription;
use App\Http\Livewire\AudioTranscription\AudioTranscription;
use App\Http\Livewire\InquiryHub\NewInquiry;
use App\Http\Livewire\Summarizer\NewSummarizer;
use App\Http\Livewire\Summarizer\SummaryView;
use App\Http\Livewire\Trash;
use App\Models\ShortLink;
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
    Route::get('/documents/blog-post/{document}/processing', ProcessingBlogPost::class)
        ->name('blog-post-processing-view');
    Route::get('/documents/blog-post/{document}', BlogPost::class)->name('blog-post-view');

    /* Audio Transcription routes */
    Route::get('/transcription/new', NewTranscription::class)->name('new-audio-transcription');
    Route::get('/documents/transcription/{document}', AudioTranscription::class)->name('transcription-view');

    /* Social media posts routes */
    Route::get('/social-media-post/create', TempNew::class)->name('create-social-media-post');
    Route::get('/social-media-post/new', NewSocialMediaPost::class)->name('new-social-media-post');
    Route::get('/documents/social-media/{document}', SocialMediaPostsManager::class)->name('social-media-view');

    /* Paraphraser routes */
    Route::get('/paraphraser/new', NewParaphraser::class)->name('new-paraphraser');
    Route::get('/documents/paraphraser/{document}', Paraphraser::class)->name('paraphrase-view');

    /* Text to Speech routes */
    Route::get('/text-to-speech/new', TextToAudio::class)->name('new-text-to-speech');
    Route::get('/text-to-speech/history', AudioHistory::class)->name('text-to-speech-history');
    Route::get('/documents/text-to-speech/{document}', TextToAudio::class)->name('text-to-speech-view');

    /* Inquiry Hub */
    Route::get('/inquiry-hub/new', NewInquiry::class)->name('new-inquiry');
    Route::get('/documents/inquiry-hub/{document}', SocialMediaPostsManager::class)->name('inquiry-view');

    /* Summarizer */
    Route::get('/summarizer/new', NewSummarizer::class)->name('new-summarizer');
    Route::get('/documents/summarizer/{document}', SummaryView::class)->name('summarizer-view');

    /* Short links */
    Route::get('/link/{shortLink}', function (string $shortLink) {
        $shortLink = ShortLink::valid()->where('link', $shortLink)->firstOrFail();
        return redirect($shortLink->target_url);
    })->name('short-link');

    /* Document routes */
    Route::get('/documents/{document}', [DocumentViewController::class, 'index'])->name('document-view');
});

/* Google Auth */

Route::get('/google/auth/redirect', function () {
    return Socialite::driver('google')->redirect();
})->name('login.google');

Route::get('/google/auth/callback', [GoogleAuthController::class, 'handleProviderCallback'])
    ->name('login.google.callback');

/* Stripe Webhook */
Route::post('/stripe/payment', [GoogleAuthController::class, 'handleProviderCallback'])
    ->name('payment.webhook');
