<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Invoices')]
class InvoiceController extends Controller
{
    #[OA\Get(
        path: '/api/invoices',
        summary: 'List invoices',
        tags: ['Invoices']
    )]
    #[OA\Response(
        response: 200,
        description: 'Invoices fetched successfully'
    )]
    public function index(): JsonResponse
    {
        $invoices = Invoice::query()
            ->with([
                'contractor',
                'items',
                'payment',
                'document',
            ])
            ->latest()
            ->get();

        return response()->json([
            'data' => $invoices,
        ]);
    }

    #[OA\Get(
        path: '/api/invoices/{invoice}',
        summary: 'Show invoice details',
        tags: ['Invoices']
    )]
    #[OA\Parameter(
        name: 'invoice',
        description: 'Invoice ID',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Invoice fetched successfully'
    )]
    #[OA\Response(
        response: 404,
        description: 'Invoice not found'
    )]
    public function show(Invoice $invoice): JsonResponse
    {
        $invoice->load([
            'contractor',
            'items',
            'payment',
            'document',
        ]);

        return response()->json([
            'data' => $invoice,
        ]);
    }
}
