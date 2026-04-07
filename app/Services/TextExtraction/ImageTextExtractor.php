<?php

namespace App\Services\TextExtraction;

use thiagoalessio\TesseractOCR\TesseractOCR;

class ImageTextExtractor
{
    public function extract(string $filePath): string
    {
        $text = (new TesseractOCR($filePath))->run();

        return trim($text);
    }
}
