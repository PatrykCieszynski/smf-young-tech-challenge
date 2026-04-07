<?php

namespace App\Services\TextExtraction;

use App\Models\Document;
use RuntimeException;

readonly class TextExtractionService
{
    public function __construct(
        private PdfTextExtractor   $pdfTextExtractor,
        private ImageTextExtractor $imageTextExtractor,
    ) {
    }

    public function extractFromDocument(Document $document, string $absolutePath): string
    {
        return match ($document->mime_type) {
            'application/pdf' => $this->pdfTextExtractor->extract($absolutePath),
            'image/jpeg', 'image/jpg', 'image/png' => $this->imageTextExtractor->extract($absolutePath),
            default => throw new RuntimeException('Unsupported document type: ' . $document->mime_type),
        };
    }
}
