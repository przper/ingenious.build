<?php

namespace Modules\Invoices\Api\Dtos;

use Ramsey\Uuid\UuidInterface;

final readonly class InvoiceData
{
    /** @param InvoiceLineData[] $lines */
    public function __construct(
        public UuidInterface $id,
        public string $status,
        public string $customerName,
        public string $customerEmail,
        public array $lines,
        public float $totalPrice,
    ) {
    }
}
