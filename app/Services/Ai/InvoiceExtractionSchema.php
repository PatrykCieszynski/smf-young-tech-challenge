<?php

namespace App\Services\Ai;

class InvoiceExtractionSchema
{
    public function toArray(): array
    {
        return [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['contractor', 'invoice', 'items', 'payment'],
            'properties' => [
                'contractor' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'required' => ['name', 'address', 'nip'],
                    'properties' => [
                        'name' => [
                            'type' => ['string', 'null'],
                        ],
                        'address' => [
                            'type' => ['string', 'null'],
                        ],
                        'nip' => [
                            'type' => ['string', 'null'],
                        ],
                    ],
                ],
                'invoice' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'required' => ['number', 'issue_date', 'due_date'],
                    'properties' => [
                        'number' => [
                            'type' => ['string', 'null'],
                        ],
                        'issue_date' => [
                            'type' => ['string', 'null'],
                        ],
                        'due_date' => [
                            'type' => ['string', 'null'],
                        ],
                    ],
                ],
                'items' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'additionalProperties' => false,
                        'required' => ['name', 'quantity', 'unit_price', 'total_value'],
                        'properties' => [
                            'name' => [
                                'type' => ['string', 'null'],
                            ],
                            'quantity' => [
                                'type' => ['number', 'null'],
                            ],
                            'unit_price' => [
                                'type' => ['number', 'null'],
                            ],
                            'total_value' => [
                                'type' => ['number', 'null'],
                            ],
                        ],
                    ],
                ],
                'payment' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'required' => ['currency', 'total_amount', 'method'],
                    'properties' => [
                        'currency' => [
                            'type' => ['string', 'null'],
                        ],
                        'total_amount' => [
                            'type' => ['number', 'null'],
                        ],
                        'method' => [
                            'type' => ['string', 'null'],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function toJson(): string
    {
        return json_encode(
            $this->toArray(),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }
}
