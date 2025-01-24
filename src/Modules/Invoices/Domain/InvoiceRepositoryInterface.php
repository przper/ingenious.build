<?php

namespace Modules\Invoices\Domain;

interface InvoiceRepositoryInterface
{
    public function get(InvoiceId $id): ?Invoice;

    public function persist(Invoice $invoice): void;
}
