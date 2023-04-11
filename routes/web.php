<?php

use App\Http\Livewire\Dashboard;
use App\Http\Livewire\MyWork;
use App\Http\Livewire\NewPost;
use App\Http\Livewire\PendingJobs;
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
    Route::get('/new', NewPost::class)->name('new-post');
    Route::get('/pending', PendingJobs::class)->name('pending-jobs');
    Route::get('/my-work', MyWork::class)->name('my-work');
    Route::get('/templates', MyWork::class)->name('templates');
    Route::delete('/text/{text-request}', MyWork::class)->name('text-request.destroy');
});
