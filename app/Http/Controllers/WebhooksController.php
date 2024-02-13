<?php

namespace App\Http\Controllers;

use App\Enums\DocumentTaskEnum;
use App\Exceptions\WebhookException;
use App\Jobs\DispatchDocumentTasks;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class WebhooksController extends Controller
{
    public function assemblyAI(Request $request)
    {
        try {
            $token = $request->bearerToken();
            $ipAddress = $request->ip();
            if ($token !== config('assemblyai.token')) {
                Log::error('Assembly AI invalid token sent', [$token]);
                abort(403);
            } elseif ($ipAddress !== '44.238.19.20') {
                Log::error('Assembly AI invalid IP address', [$ipAddress]);
                abort(403);
            }

            $params = $request->all();
            if ($params['status'] === 'completed') {
                $document = Document::findOrFail($params['document_id']);
                DocumentRepository::createTask(
                    $document->id,
                    DocumentTaskEnum::PROCESS_TRANSCRIPTION_RESULTS,
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
        } catch (HttpException $e) {
            if ($e->getStatusCode() === 403) {
                throw new HttpException($e->getStatusCode(), $e->getMessage());
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new WebhookException($e->getMessage());
        }
    }
}
