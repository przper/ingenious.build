<?php

namespace Modules\Invoices\Api;

use Modules\Invoices\Api\Dto\InvoiceData;

interface InvoiceFacadeInterface
{
    public function get(string $id): ?InvoiceData;

    public function create(string $customerName, string $customerEmail, array $lines): string;

    public function send(string $id, ?string $subject = null, ?string $email = null): void;

    public function confirmDelivery(string $id): void;
}
