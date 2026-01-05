<?php

namespace App\Events;

use App\Models\DocumentRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class DocumentUploaded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public function __construct(
        public DocumentRequest $documentRequest
    ) {}


    public function getFileName(): ?string
    {
        return $this->documentRequest->file_name;
    }


    public function getFilePath(): ?string
    {
        return $this->documentRequest->file_path;
    }


    public function getFileSize(): ?int
    {
        return $this->documentRequest->file_size;
    }

    public function getFileExtension(): ?string
    {
        if ($fileName = $this->getFileName()) {
            return pathinfo($fileName, PATHINFO_EXTENSION);
        }
        return null;
    }
}
