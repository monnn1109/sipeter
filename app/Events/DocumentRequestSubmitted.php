<?php

namespace App\Events;

use App\Models\DocumentRequest;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentRequestSubmitted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public DocumentRequest $documentRequest
    ) {}
}
