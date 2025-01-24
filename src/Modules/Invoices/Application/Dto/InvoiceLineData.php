<?php

namespace Modules\Invoices\Application\Dto;

final readonly class InvoiceLineData
{
    public function __construct(
        public string $id,
        public string $invoiceId,
        public string $productName,
        public float $unitPrice,
        public float $quantity,
        public float $totalPrice,
    ) {
    }
}
