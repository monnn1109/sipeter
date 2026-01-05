<?php
namespace App\Events;

use App\Models\DocumentRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentSelfConfirmed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public DocumentRequest $documentRequest;
    public array $metadata;

    public function __construct(DocumentRequest $documentRequest, array $metadata = [])
    {
        $this->documentRequest = $documentRequest;
        $this->metadata = $metadata;
    }
}
