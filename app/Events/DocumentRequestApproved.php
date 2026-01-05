<?php

namespace App\Events;

use App\Models\DocumentRequest;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentRequestApproved
{
    use Dispatchable, SerializesModels;


    public function __construct(
        public DocumentRequest $documentRequest,
        public bool $requiresSignature = true
    ) {}
}
