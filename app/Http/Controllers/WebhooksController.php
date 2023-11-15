<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhooksController extends Controller
{
    public function assemblyAI(Request $request)
    {
        $token = $request->bearerToken();
        $ipAddress = $request->ip();
        if ($token !== config('assemblyai.token')) {
            Log::error('Assembly AI invalid token sent', $token);
            abort(403);
        } elseif ($ipAddress !== '44.238.19.20') {
            Log::error('Assembly AI invalid IP address', $ipAddress);
            abort(403);
        }

        Log::debug($request->all());
    }
}
