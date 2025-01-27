<?php

namespace Modules\Invoices\Api\Dtos;

final readonly class InvoiceLineCreateData
{
    public function __construct(
        public string $productName,
        public float $unitPrice,
        public float $quantity,
    ) {
    }
}
