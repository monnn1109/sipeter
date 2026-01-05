<?php

namespace App\Events;

use App\Models\DocumentRequest;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentReadyForPickup
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public DocumentRequest $documentRequest
    ) {}
}
