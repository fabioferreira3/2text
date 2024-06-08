<?php

namespace App\Domain\Agents\Listeners;

use App\Domain\AgentsEvents\PollRunFailed;
use Illuminate\Support\Facades\Log;

class HandleFailedRun
{
    /**
     * Handle the event.
     *
     * @param PollRunFailed $event
     * @return void
     */
    public function handle(PollRunFailed $event)
    {
        Log::error($event->threadRun->run_id . ' failed');
        $event->threadRun->update([
            'failed_at' => $event->request->failedAt,
            'canceled_at' => $event->request->cancelledAt,
            'status' => $event->request->status,
            'meta' => [
                'error' => $event->request->lastError,
                'incomplete_details' => $event->request->incompleteDetails
            ]
        ]);
    }
}
