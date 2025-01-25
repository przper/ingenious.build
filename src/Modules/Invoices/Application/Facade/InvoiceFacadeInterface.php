<?php

namespace Modules\Invoices\Application\Facade;

use Modules\Invoices\Application\Dto\InvoiceData;

interface InvoiceFacadeInterface
{
    public function get(string $id): ?InvoiceData;

    public function create(string $customerName, string $customerEmail, array $lines): string;

    public function send(string $id, ?string $subject, ?string $email = null): void;
    public function send(string $id, ?string $subject = null, ?string $email = null): void;
}
