<?php

namespace App\Events;

use App\Models\DocumentRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class DocumentCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public function __construct(
        public DocumentRequest $documentRequest
    ) {}


    public function isCompletedByGuest(): bool
    {
        return $this->documentRequest->isGuestRequest();
    }


    public function isCompletedByInternal(): bool
    {
        return $this->documentRequest->isInternalRequest();
    }


    public function getApplicantName(): string
    {
        return $this->documentRequest->applicant_name ?? 'Unknown';
    }

    public function getDocumentTypeName(): string
    {
        return $this->documentRequest->documentType->name ?? 'Unknown Document';
    }

    public function getCompletedAt(): ?\Carbon\Carbon
    {
        return $this->documentRequest->completed_at;
    }
}
