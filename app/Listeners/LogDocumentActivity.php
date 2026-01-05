<?php

namespace App\Listeners;

use App\Events\{
    DocumentRequestSubmitted,
    DocumentRequestApproved,
    DocumentRequestRejected,
    DocumentReadyForPickup,
    DocumentUploaded,
    DocumentPickedUp,
    DocumentDownloaded,
    DocumentCompleted // ✅ NEW
};
use App\Services\DocumentHistoryService;
use Illuminate\Support\Facades\Log;

class LogDocumentActivity
{
    public function __construct(
        private DocumentHistoryService $historyService
    ) {}

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            match (true) {
                $event instanceof DocumentRequestSubmitted => $this->historyService->logSubmitted($event->documentRequest),
                $event instanceof DocumentRequestApproved => $this->historyService->logApproved($event->documentRequest),
                $event instanceof DocumentRequestRejected => $this->historyService->logRejected($event->documentRequest),
                $event instanceof DocumentUploaded => $this->historyService->logUploaded($event->documentRequest),
                $event instanceof DocumentReadyForPickup => $this->historyService->logReady($event->documentRequest),
                $event instanceof DocumentPickedUp => $this->historyService->logPickedUp($event->documentRequest),
                $event instanceof DocumentDownloaded => $this->historyService->logDownloaded($event->documentRequest, $event->metadata ?? []),
                $event instanceof DocumentCompleted => $this->historyService->logCompleted($event->documentRequest), // ✅ NEW
                default => null
            };
        } catch (\Exception $e) {
            Log::error('Failed to log document activity', [
                'event' => get_class($event),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * ✅ FIX: Add __invoke() method for Laravel Event Auto-Discovery
     * This allows the listener to be called as an invokable class
     */
    public function __invoke($event): void
    {
        $this->handle($event);
    }
}
