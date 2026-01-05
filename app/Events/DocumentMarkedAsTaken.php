<?php

namespace App\Events;

use App\Models\{DocumentRequest, User};
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentMarkedAsTaken
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public DocumentRequest $documentRequest,
        public ?User $markedBy = null,
        public string $markedByRole = 'user', 
        public ?string $notes = null
    ) {}
}
