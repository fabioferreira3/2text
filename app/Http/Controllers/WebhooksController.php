<?php

namespace App\Http\Controllers;

use App\Enums\DocumentTaskEnum;
use App\Jobs\DispatchDocumentTasks;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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

        $params = $request->all();
        if ($params['status'] === 'completed') {
            $document = Document::findOrFail($params['document_id']);
            DocumentRepository::createTask(
                $document->id,
                DocumentTaskEnum::POST_PROCESS_AUDIO,
                [
                    'order' => 1,
                    'process_id' => Str::uuid(),
                    'meta' => [
                        'pending_task_id' => $params['task_id'],
                        'transcript_id' => $params['transcript_id']
                    ]
                ]
            );

            DispatchDocumentTasks::dispatch($document);
        }
    }
}
