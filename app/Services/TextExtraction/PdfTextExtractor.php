<?php

namespace App\Services\TextExtraction;

use RuntimeException;
use Spatie\PdfToText\Pdf;

class PdfTextExtractor
{
    public function extract(string $filePath): string
    {
        $binaryPath = config('services.pdftotext.binary_path');

        if (!$binaryPath) {
            throw new RuntimeException('PDFTOTEXT_PATH is not configured.');
        }

        $text = Pdf::getText($filePath, $binaryPath);

        return trim($text);
    }
}
