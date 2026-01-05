<?php

namespace App\Events;

use App\Models\{DocumentRequest, DocumentSignature, SignatureAuthority};
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SignatureUploaded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public DocumentRequest $documentRequest,
        public DocumentSignature $signature,
        public SignatureAuthority $authority       
    ) {}
}
