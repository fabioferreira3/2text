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
use App\Http\Livewire\Templates;
use App\Http\Livewire\TextToAudio\AudioHistory;
use App\Http\Livewire\TextToAudio\TextToAudio;
use App\Http\Livewire\AudioTranscription\NewTranscription;
use App\Http\Livewire\AudioTranscription\AudioTranscription;
use App\Http\Livewire\AudioTranscription\Dashboard as AudioTranscriptionDashboard;
use App\Http\Livewire\Blog\Dashboard as BlogDashboard;
use App\Http\Livewire\InsightHub\Dashboard as InsightHubDashboard;
use App\Http\Livewire\InsightHub\InsightView;
use App\Http\Livewire\Paraphraser\Dashboard as ParaphraserDashboard;
use App\Http\Livewire\Product\Purchase;
use App\Http\Livewire\Purchase\CheckoutSuccess;
use App\Http\Livewire\SocialMediaPost\Dashboard as SocialMediaPostDashboard;
use App\Http\Livewire\Summarizer\Dashboard as SummarizerDashboard;
use App\Http\Livewire\Summarizer\NewSummarizer;
use App\Http\Livewire\Summarizer\SummaryView;
use App\Http\Livewire\Trash;
use App\Models\ShortLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Cashier\Cashier;
use Laravel\Socialite\Facades\Socialite;
use Stripe\Stripe;

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
    Route::get('/billing-portal', function (Request $request) {
        return $request->user()->redirectToBillingPortal();
    });

    Route::get('/purchase', Purchase::class)->name('purchase');

    Route::get('/charge', function (Request $request) {
        $quantity = 400;
        return $request->user()->checkout(
            ['price_1OdeFqEjLWGu0g9vVJeUQOso' => $quantity],
            [
                'success_url' => route('checkout-success') . '?session_id={CHECKOUT_SESSION_ID}',
                'metadata' => [
                    'product_id' => 'ef2fd99f-55a6-4435-9128-c75df27fd13c',
                    'quantity' => $quantity
                ]
            ]
        );
    });

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
