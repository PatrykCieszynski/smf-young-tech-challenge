<?php

namespace App\Services\Ai;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

readonly class DocumentAiExtractionService
{
    public function __construct(
        private InvoiceExtractionSchema $invoiceExtractionSchema,
    ) {
    }

    public function extract(string $ocrText): array
    {
        $schema = $this->invoiceExtractionSchema->toArray();
        $schemaJson = $this->invoiceExtractionSchema->toJson();

        $prompt = <<<PROMPT
            Extract structured invoice data from the OCR text below.

            Rules:
            - Return only data that is present in the text.
            - Do not guess missing values.
            - Use null for missing scalar values.
            - Use an empty array when no items are found.
            - Keep numbers as numbers.
            - contractor means the seller, issuer, or service provider shown on the invoice
            - do not use the buyer, bill-to party, or customer as contractor
            - if both seller and buyer are present, choose the seller as contractor
            - Preserve invoice number, dates, currency, and payment method exactly if possible.
            - Put NIP, VAT ID, or any other tax identifier into contractor.nip.
            - Put currency, total amount, and payment method into payment.
            - Put document number and dates into invoice.

            Expected JSON schema:
            {$schemaJson}

            OCR text:
            {$ocrText}
        PROMPT;

        try {
            $response = Http::timeout(120)
                ->acceptJson()
                ->post(rtrim(config('services.ollama.url'), '/') . '/api/generate', [
                    'model' => config('services.ollama.model'),
                    'prompt' => $prompt,
                    'format' => $schema,
                    'stream' => false,
                ]);
        } catch (ConnectionException $e) {
            throw new RuntimeException(
                'Could not connect to Ollama. Make sure the Ollama server is running and the URL is correct.',
                previous: $e,
            );
        }

        if ($response->failed()) {
            throw new RuntimeException(
                'Ollama request failed with status ' . $response->status() . '.'
            );
        }

        $payload = $response->json();

        if (!is_array($payload)) {
            throw new RuntimeException('Invalid Ollama response payload.');
        }

        $rawResponse = $payload['response'] ?? null;

        if (!is_string($rawResponse) || trim($rawResponse) === '') {
            throw new RuntimeException('Ollama returned an empty response.');
        }

        $decoded = json_decode($rawResponse, true);

        if (!is_array($decoded)) {
            throw new RuntimeException('Ollama response is not valid JSON.');
        }

        return [
            'raw_response' => $rawResponse,
            'parsed_data' => $decoded,
        ];
    }
}
