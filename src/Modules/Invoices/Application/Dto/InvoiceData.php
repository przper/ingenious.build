<?php

namespace Modules\Invoices\Application\Dto;

final readonly class InvoiceData
{
    /** @param InvoiceLineData[] $lines */
    public function __construct(
        public string $id,
        public string $customerName,
        public string $customerEmail,
        public array $lines,
        public float $totalPrice,
    ) {
    }
}
