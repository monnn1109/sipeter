<?php

namespace App\Events;

use App\Models\DocumentRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class DocumentDownloaded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public function __construct(
        public DocumentRequest $documentRequest,
        public array $metadata = []
    ) {}


    public function getDownloadedBy(): ?string
    {
        return $this->metadata['downloaded_by']
            ?? $this->metadata['downloadedBy']
            ?? null;
    }

    public function getIpAddress(): ?string
    {
        return $this->metadata['ip_address']
            ?? $this->metadata['ipAddress']
            ?? null;
    }

    public function getUserAgent(): ?string
    {
        return $this->metadata['user_agent']
            ?? $this->metadata['userAgent']
            ?? null;
    }


    public function __get($name)
    {
        $snakeName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name));

        return $this->metadata[$name]
            ?? $this->metadata[$snakeName]
            ?? null;
    }
}
