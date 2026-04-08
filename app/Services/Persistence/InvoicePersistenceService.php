<?php

namespace App\Services\Persistence;

use App\Models\Contractor;
use App\Models\Document;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class InvoicePersistenceService
{
    public function persist(Document $document, array $parsedData): Invoice
    {
        return DB::transaction(function () use ($document, $parsedData) {
            $invoiceData = $parsedData['invoice'] ?? [];
            $contractorData = $parsedData['contractor'] ?? [];
            $itemsData = $parsedData['items'] ?? [];
            $paymentData = $parsedData['payment'] ?? [];

            $existingInvoice = $document->invoice;

            if ($existingInvoice !== null) {
                $existingInvoice->items()->delete();
                $existingInvoice->payment()->delete();
                $existingInvoice->delete();
            }

            $contractor = Contractor::create([
                'name' => $contractorData['name'] ?? null,
                'address' => $contractorData['address'] ?? null,
                'nip' => $contractorData['nip'] ?? null,
            ]);

            $invoice = Invoice::create([
                'document_id' => $document->id,
                'contractor_id' => $contractor->id,
                'number' => $invoiceData['number'] ?? null,
                'issue_date' => $invoiceData['issue_date'] ?? null,
                'due_date' => $invoiceData['due_date'] ?? null,
            ]);

            $normalizedItems = array_map(function (array $item): array {
                return [
                    'name' => $item['name'] ?? null,
                    'quantity' => $item['quantity'] ?? null,
                    'unit_price' => $item['unit_price'] ?? null,
                    'total_value' => $item['total_value'] ?? null,
                ];
            }, is_array($itemsData) ? $itemsData : []);

            if ($normalizedItems !== []) {
                $invoice->items()->createMany($normalizedItems);
            }

            $invoice->payment()->create([
                'currency' => $paymentData['currency'] ?? null,
                'total_amount' => $paymentData['total_amount'] ?? null,
                'method' => $paymentData['method'] ?? null,
            ]);

            return $invoice->load([
                'contractor',
                'items',
                'payment',
                'document',
            ]);
        });
    }
}
