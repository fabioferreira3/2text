<?php

use App\Http\Livewire\Dashboard;
use App\Http\Livewire\DocumentView;
use App\Http\Livewire\MyDocuments;
use App\Http\Livewire\NewPost;
use App\Http\Livewire\PendingJobs;
use App\Http\Livewire\Templates;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/blog/new', NewPost::class)->name('new-post');
    Route::get('/pending', PendingJobs::class)->name('pending-jobs');
    Route::get('/templates', Templates::class)->name('templates');
    Route::get('/documents/{document}', DocumentView::class)->name('document-view');
    Route::delete('/text/{text-request}', MyDocuments::class)->name('text-request.destroy');
});
