<?php

namespace Modules\Invoices\Api\Dtos;

final readonly class InvoiceData
{
    /** @param InvoiceLineData[] $lines */
    public function __construct(
        public string $id,
        public string $status,
        public string $customerName,
        public string $customerEmail,
        public array $lines,
        public float $totalPrice,
    ) {
    }
}
