<?php

namespace Modules\Invoices\Infrastructure\Persistance\Eloquent;

use Illuminate\Support\Facades\DB;
use Modules\Invoices\Domain\Invoice as DomainInvoice;
use Modules\Invoices\Domain\InvoiceId;
use Modules\Invoices\Domain\InvoiceLine as DomainInvoiceLine;
use Modules\Invoices\Domain\InvoiceLineId;
use Modules\Invoices\Domain\InvoiceLines;
use Modules\Invoices\Domain\InvoiceRepositoryInterface;
use Modules\Invoices\Domain\Price;
use Modules\Invoices\Domain\Quantity;
use Modules\Invoices\Infrastructure\Persistance\Eloquent\Model\Invoice;
use Modules\Invoices\Infrastructure\Persistance\Eloquent\Model\InvoiceLine;
use Modules\Shared\Domain\Text;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function get(InvoiceId $id): ?DomainInvoice
    {
        $model = Invoice::query()->with('lines')->find($id->value);

        if ($model === null) {
            return null;
        }

        $lines = InvoiceLines::create();

        foreach ($model->lines as $line) {
            $lines->addLine(DomainInvoiceLine::create(
                id: new InvoiceLineId($line->id),
                productName: Text::create($line->name),
                quantity: Quantity::create($line->quantity),
                unitPrice: Price::create($line->price),
            ));
        }

        return DomainInvoice::restore(
            id: new InvoiceId($model->id),
            status: $model->status,
            customerName: Text::create($model->customer_name),
            customerEmail: Text::create($model->customer_email),
            lines: $lines,
        );
    }

    public function persist(DomainInvoice $invoice): void
    {
        DB::transaction(function () use ($invoice) {
            $model = new Invoice();
            $model->id = (string) $invoice->getId();
            $model->status = $invoice->getStatus();
            $model->customerName = (string) $invoice->getCustomerName();
            $model->customerEmail = (string) $invoice->getCustomerEmail();

            $model->lines()->truncate();
            /** @var DomainInvoiceLine $line */
            foreach ($invoice->getLines() as $line) {
                $modelLine = new InvoiceLine();
                $modelLine->id = (string) $line->getId();
                $modelLine->productName = (string) $line->getProductName();
                $modelLine->quantity = $line->getQuantity()->toFloat();
                $modelLine->unitPrice = $line->getUnitPrice()->toFloat();

                $model->lines()->save($modelLine);
            }

            $model->save();
        });
    }
}
