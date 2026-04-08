<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Models\Document;
use App\Services\Ai\DocumentAiExtractionService;
use App\Services\Persistence\InvoicePersistenceService;
use App\Services\TextExtraction\TextExtractionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;
use Throwable;

#[OA\Tag(name: 'Documents')]
class DocumentController extends Controller
{
    #[OA\Get(
        path: '/api/documents',
        summary: 'List documents',
        tags: ['Documents']
    )]
    #[OA\Response(
        response: 200,
        description: 'Documents fetched successfully'
    )]
    public function index(): JsonResponse
    {
        $documents = Document::query()
            ->latest()
            ->get();

        return response()->json([
            'data' => $documents,
        ]);
    }

    #[OA\Get(
        path: '/api/documents/{document}',
        summary: 'Show document details',
        tags: ['Documents']
    )]
    #[OA\Parameter(
        name: 'document',
        description: 'Document ID',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Document fetched successfully'
    )]
    #[OA\Response(
        response: 404,
        description: 'Document not found'
    )]
    public function show(Document $document): JsonResponse
    {
        return response()->json([
            'data' => $document,
        ]);
    }

    #[OA\Post(
        path: '/api/documents',
        summary: 'Upload a document',
        tags: ['Documents']
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                required: ['file'],
                properties: [
                    new OA\Property(
                        property: 'file',
                        type: 'string',
                        format: 'binary'
                    ),
                ]
            )
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Document uploaded successfully'
    )]
    #[OA\Response(
        response: 422,
        description: 'Validation failed'
    )]
    public function store(StoreDocumentRequest $request): JsonResponse
    {
        $uploadedFile = $request->file('file');

        $storedPath = $uploadedFile->store('documents', 'local');

        $document = Document::create([
            'original_name' => $uploadedFile->getClientOriginalName(),
            'stored_path' => $storedPath,
            'mime_type' => $uploadedFile->getMimeType(),
            'file_size' => $uploadedFile->getSize(),
            'status' => 'uploaded',
            'ocr_text' => null,
            'ai_raw_response' => null,
            'error_message' => null,
            'processed_at' => null,
        ]);

        return response()->json([
            'message' => 'Document uploaded successfully.',
            'document' => $document,
        ], 201);
    }

    #[OA\Delete(
        path: '/api/documents/{document}',
        summary: 'Delete a document',
        tags: ['Documents']
    )]
    #[OA\Parameter(
        name: 'document',
        description: 'Document ID',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Document deleted successfully'
    )]
    #[OA\Response(
        response: 404,
        description: 'Document not found'
    )]
    public function destroy(Document $document): JsonResponse
    {
        if (Storage::disk('local')->exists($document->stored_path)) {
            Storage::disk('local')->delete($document->stored_path);
        }

        $document->delete();

        return response()->json([
            'message' => 'Document deleted successfully.',
        ]);
    }

    #[OA\Post(
        path: '/api/documents/{document}/process',
        summary: 'Process a document',
        tags: ['Documents']
    )]
    #[OA\Parameter(
        name: 'document',
        description: 'Document ID',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Document processed successfully'
    )]
    #[OA\Response(
        response: 404,
        description: 'Stored file was not found'
    )]
    #[OA\Response(
        response: 422,
        description: 'No text could be extracted from the document'
    )]
    #[OA\Response(
        response: 500,
        description: 'Document processing failed'
    )]
    public function process(
        Document $document,
        TextExtractionService $textExtractionService,
        DocumentAiExtractionService $documentAiExtractionService,
        InvoicePersistenceService $invoicePersistenceService,
    ): JsonResponse {
        if (!Storage::disk('local')->exists($document->stored_path)) {
            $document->update([
                'status' => 'failed',
                'error_message' => 'Stored file was not found.',
                'processed_at' => null,
            ]);

            return response()->json([
                'message' => 'Document processing failed.',
                'document' => $document->fresh(),
            ], 404);
        }

        if ($document->invoice !== null) {
            return response()->json([
                'message' => 'Document has already been processed.',
                'document' => $document->fresh(),
            ], 409);
        }

        $document->update([
            'status' => 'processing',
            'error_message' => null,
        ]);

        try {
            $absolutePath = Storage::disk('local')->path($document->stored_path);

            $extractedText = $textExtractionService->extractFromDocument($document, $absolutePath);

            if ($extractedText === '') {
                $document->update([
                    'status' => 'failed',
                    'ocr_text' => null,
                    'ai_raw_response' => null,
                    'error_message' => 'No text could be extracted from the document.',
                    'processed_at' => null,
                ]);

                return response()->json([
                    'message' => 'Document processing failed.',
                    'document' => $document->fresh(),
                ], 422);
            }

            $aiResult = $documentAiExtractionService->extract($extractedText);

            $invoice = $invoicePersistenceService->persist($document, $aiResult['parsed_data']);

            $document->update([
                'status' => 'processed',
                'ocr_text' => $extractedText,
                'ai_raw_response' => $aiResult['raw_response'],
                'error_message' => null,
                'processed_at' => now(),
            ]);

            return response()->json([
                'message' => 'Document processed successfully.',
                'document' => $document->fresh(),
                'invoice' => $invoice,
                'parsed_data' => $aiResult['parsed_data'],
            ]);
        } catch (Throwable $exception) {
            $document->update([
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
                'processed_at' => null,
            ]);

            return response()->json([
                'message' => 'Document processing failed.',
                'document' => $document->fresh(),
            ], 500);
        }
    }
}
