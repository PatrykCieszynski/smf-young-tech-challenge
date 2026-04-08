<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    description: 'API for document upload, OCR, AI extraction, and invoice persistence.',
    title: 'SMF Young Tech Challenge API'
)]
#[OA\Server(
    url: 'http://127.0.0.1:8000',
    description: 'Local development server'
)]
class OpenApiSpec
{
}
