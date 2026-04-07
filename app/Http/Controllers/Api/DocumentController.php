<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Models\Document;
use App\Services\TextExtraction\TextExtractionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Throwable;

class DocumentController extends Controller
{
    public function index(): JsonResponse
    {
        $documents = Document::query()
            ->latest()
            ->get();

        return response()->json([
            'data' => $documents,
        ]);
    }

    public function show(Document $document): JsonResponse
    {
        return response()->json([
            'data' => $document,
        ]);
    }

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

    public function process(Document $document, TextExtractionService $textExtractionService): JsonResponse
    {
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

        if ($document->status === 'processed') {
            return response()->json([
                'message' => 'Document is already processed.',
                'document' => $document,
            ]);
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
                    'error_message' => 'No text could be extracted from the document.',
                    'processed_at' => null,
                ]);

                return response()->json([
                    'message' => 'Document processing failed.',
                    'document' => $document->fresh(),
                ], 422);
            }

            $document->update([
                'status' => 'processed',
                'ocr_text' => $extractedText,
                'error_message' => null,
                'processed_at' => now(),
            ]);

            return response()->json([
                'message' => 'Document processed successfully.',
                'document' => $document->fresh(),
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
