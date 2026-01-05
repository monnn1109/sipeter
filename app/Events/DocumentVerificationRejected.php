<?php

namespace App\Events;

use App\Models\DocumentRequest;
use App\Models\SignatureAuthority;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentVerificationRejected
{
    use Dispatchable, SerializesModels;


    public function __construct(
        public DocumentRequest $documentRequest,
        public SignatureAuthority $authority,
        public string $reason
    ) {}
}
