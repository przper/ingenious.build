<?php

namespace Modules\Invoices\Api\Dtos;

use Ramsey\Uuid\UuidInterface;

final readonly class InvoiceLineData
{
    public function __construct(
        public UuidInterface $id,
        public string $invoiceId,
        public string $productName,
        public float $unitPrice,
        public float $quantity,
        public float $totalPrice,
    ) {
    }
}
