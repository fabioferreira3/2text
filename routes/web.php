<?php

use App\Http\Controllers\DocumentViewController;
use App\Http\Controllers\GoogleAuthController;
use App\Livewire\Dashboard;
use App\Livewire\Blog\BlogPost;
use App\Livewire\Blog\NewPost;
use App\Livewire\Blog\ProcessingBlogPost;
use App\Livewire\Paraphraser\NewParaphraser;
use App\Livewire\Paraphraser\Paraphraser;
use App\Livewire\SocialMediaPost\NewSocialMediaPost;
use App\Livewire\SocialMediaPost\SocialMediaPostsManager;
use App\Livewire\Templates;
use App\Livewire\TextToAudio\AudioHistory;
use App\Livewire\TextToAudio\TextToAudio;
use App\Livewire\AudioTranscription\NewTranscription;
use App\Livewire\AudioTranscription\AudioTranscription;
use App\Livewire\AudioTranscription\Dashboard as AudioTranscriptionDashboard;
use App\Livewire\Blog\Dashboard as BlogDashboard;
use App\Livewire\InsightHub\Dashboard as InsightHubDashboard;
use App\Livewire\InsightHub\InsightView;
use App\Livewire\Paraphraser\Dashboard as ParaphraserDashboard;
use App\Http\Livewire\Product\Checkout;
use App\Livewire\Product\Purchase;
use App\Livewire\Purchase\CheckoutSuccess;
use App\Livewire\SocialMediaPost\Dashboard as SocialMediaPostDashboard;
use App\Livewire\Summarizer\Dashboard as SummarizerDashboard;
use App\Livewire\Summarizer\NewSummarizer;
use App\Livewire\Summarizer\SummaryView;
use App\Livewire\Trash;
use App\Models\ShortLink;
use Illuminate\Http\Request;
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
    // 'checktoken',
    'maintenance'
])->group(function () {
    Route::get('/', function () {
        return redirect('/tools');
    });
    Route::get('/dashboard', Dashboard::class)->name('home');
    Route::get('/dashboard?tab=images', Dashboard::class)->name('my-images');
    Route::get('/dashboard/trash', Trash::class)->name('trash');
    Route::get('/tools', Templates::class)->name('tools');

    /* Blog routes */
    Route::get('/blog', BlogDashboard::class)->name('blog-dashboard');
    Route::get('/blog/new', NewPost::class)->name('new-post');
    Route::get('/documents/blog-post/{document}/processing', ProcessingBlogPost::class)
        ->name('blog-post-processing-view');
    Route::get('/documents/blog-post/{document}', BlogPost::class)->name('blog-post-view');

    /* Audio Transcription routes */
    Route::get('/transcription', AudioTranscriptionDashboard::class)->name('transcription-dashboard');
    Route::get('/transcription/new', NewTranscription::class)->name('new-audio-transcription');
    Route::get('/documents/transcription/{document}', AudioTranscription::class)->name('transcription-view');

    /* Social media posts routes */
    Route::get('/social-media-post', SocialMediaPostDashboard::class)->name('social-media-dashboard');
    Route::get('/social-media-post/new', NewSocialMediaPost::class)->name('new-social-media-post');
    Route::get('/documents/social-media/{document}', SocialMediaPostsManager::class)->name('social-media-view');

    /* Paraphraser routes */
    Route::get('/paraphraser', ParaphraserDashboard::class)->name('paraphraser-dashboard');
    Route::get('/paraphraser/new', NewParaphraser::class)->name('new-paraphraser');
    Route::get('/documents/paraphraser/{document}', Paraphraser::class)->name('paraphrase-view');

    /* Text to Speech routes */
    Route::get('/text-to-audio/new', TextToAudio::class)->name('new-text-to-audio');
    Route::get('/text-to-audio/history', AudioHistory::class)->name('text-to-audio-history');
    Route::get('/documents/text-to-audio/{document}', TextToAudio::class)->name('text-to-audio-view');

    /* Insight Hub */
    Route::get('/insight-hub', InsightHubDashboard::class)->name('insight-dashboard');
    Route::get('/documents/insight-hub/{document}', InsightView::class)->name('insight-view');

    /* Summarizer */
    Route::get('/summarizer', SummarizerDashboard::class)->name('summarizer-dashboard');
    Route::get('/summarizer/new', NewSummarizer::class)->name('new-summarizer');
    Route::get('/documents/summarizer/{document}', SummaryView::class)->name('summary-view');

    /* Short links */
    Route::get('/link/{shortLink}', function (string $shortLink) {
        $shortLink = ShortLink::valid()->where('link', $shortLink)->firstOrFail();
        return redirect($shortLink->target_url);
    })->name('short-link');

    /* Document routes */
    Route::get('/documents/{document}', [DocumentViewController::class, 'index'])->name('document-view');

    /* Billing portal */
    Route::get('/billing-management', function (Request $request) {
        return $request->user()->redirectToBillingPortal();
    })->name('billing-management');

    Route::get('/purchase', Purchase::class)->name('purchase');
    Route::get('/subscription-checkout', function (Request $request) {
        return $request->user()
            ->newSubscription('default', 'price_1Obw79EjLWGu0g9vrEaHEpxm')
            ->checkout([
                'success_url' => route('checkout-success'),
                'cancel_url' => route('subscription-checkout'),
            ]);
    })->name('subscription-checkout');
    Route::get('/checkout/success', CheckoutSuccess::class)->name('checkout-success');
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

Route::get('/voices', function () {
    // $faker = app(Faker\Generator::class);
    // $client = app(Voice::class);
    // $voices = $client->getAll();
    // return $voices;
    // foreach ($voices as $voice) {
    //     ModelsVoice::create([
    //         'external_id' => $voice['voice_id'],
    //         'name' => $faker->firstName($voice['labels']['gender'] ?? 'male'),
    //         'provider' => 'ElevenLabs',
    //         'model' => AIModel::ELEVEN_LABS->value,
    //         'preview_url' => $voice['preview_url'],
    //         'meta' => [...$voice['labels']]
    //     ]);
    // }
    //return true;
});
