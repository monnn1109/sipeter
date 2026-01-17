<?php

namespace App\Events;

use App\Models\{DocumentRequest, DocumentVerification};
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentVerificationRejected
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public DocumentRequest $documentRequest,
        public DocumentVerification $verification,
        public string $reason
    ) {}
}
