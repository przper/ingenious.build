<?php

namespace Modules\Invoices\Application\Facade;

use Modules\Invoices\Application\Dto\InvoiceData;
use Modules\Invoices\Application\Dto\InvoiceLineCreateData;
use Modules\Invoices\Application\Dto\InvoiceLineData;
use Modules\Invoices\Domain\Invoice;
use Modules\Invoices\Domain\InvoiceId;
use Modules\Invoices\Domain\InvoiceLine;
use Modules\Invoices\Domain\InvoiceLineId;
use Modules\Invoices\Domain\InvoiceRepositoryInterface;
use Modules\Invoices\Domain\Price;
use Modules\Invoices\Domain\Quantity;
use Modules\Notifications\Api\Dtos\NotifyData;
use Modules\Notifications\Api\NotificationFacadeInterface;
use Modules\Shared\Domain\Service\UuidService;
use Modules\Shared\Domain\Text;
use Ramsey\Uuid\Uuid;

final readonly class InvoiceFacade implements InvoiceFacadeInterface
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private NotificationFacadeInterface $notifications,
    ) {
    }

    public function get(string $id): ?InvoiceData
    {
        $invoice = $this->invoiceRepository->get(new InvoiceId($id));

        if ($invoice === null) {
            return null;
        }

        $lines = [];
        foreach ($invoice->getLines() as $line) {
            $lines[] = new InvoiceLineData(
                id: (string) $line->getId(),
                invoiceId: (string) $invoice->getId(),
                productName: (string) $line->getProductName(),
                unitPrice: $line->getUnitPrice()->toFloat(),
                quantity: $line->getQuantity()->toFloat(),
                totalPrice: $line->getTotalPrice()->toFloat(),
            );
        }

        return new InvoiceData(
            id: (string) $invoice->getId(),
            status: $invoice->getStatus()->value,
            customerName: (string) $invoice->getCustomerName(),
            customerEmail: (string) $invoice->getCustomerEmail(),
            lines: $lines,
            totalPrice: $invoice->getTotalPrice()->toFloat(),
        );
    }

    /** @param InvoiceLineCreateData[] $lines */
    public function create(string $customerName, string $customerEmail, array $lines): string
    {
        $invoiceId = new InvoiceId(UuidService::generateNew());

        $invoice = Invoice::create(
            id: $invoiceId,
            customerName: Text::create($customerName),
            customerEmail: Text::create($customerEmail),
        );

        foreach ($lines as $line) {
            $invoice->addLine(InvoiceLine::create(
                id: new InvoiceLineId(UuidService::generateNew()),
                productName: Text::create($line->productName),
                quantity: Quantity::create($line->quantity),
                unitPrice: Price::create($line->unitPrice),
            ));
        }

        $this->invoiceRepository->persist($invoice);

        return $invoiceId;
    }

    public function send(string $id, ?string $subject = null, ?string $email = null): void
    {
        $invoice = $this->invoiceRepository->get(new InvoiceId($id));

        if ($invoice === null) {
            return;
        }

        if ($email === null) {
            $email = $invoice->getCustomerEmail();
        }

        $data = new NotifyData(
            resourceId: Uuid::fromString($id),
            toEmail: $email,
            subject: $subject ?? "New Invoice #$id",
            message: "You have been sent a new Invoice",
        );

        $this->notifications->notify($data);

        $invoice->send();

        $this->invoiceRepository->persist($invoice);
    }

    public function confirmDelivery(string $id): void
    {
        $invoice = $this->invoiceRepository->get(new InvoiceId($id));

        if ($invoice === null) {
            return;
        }

        $invoice->confirmDelivery();

        $this->invoiceRepository->persist($invoice);
    }
}
