<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // if (Auth::check() && !Auth::user()->hasRole('admin') && Auth::user()->token_id == null) {
        //     // Redirect to profile edit page if token_id is null
        //     if ($request->path() != 'user/profile') {
        //         session()->flash('error', 'Please provide a valid access token to start using the app.');
        //         return redirect('/user/profile');
        //     }
        // }
        return $next($request);
    }
}
