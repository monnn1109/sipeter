<?php

namespace App\Events;

use App\Models\{DocumentRequest, DocumentSignature, SignatureAuthority, User};
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SignatureRejected
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public DocumentSignature $signature,
        public DocumentRequest $documentRequest,
        public SignatureAuthority $authority,
        public User $rejectedBy,
        public string $reason
    ) {}
}
