<?php

namespace Modules\Invoices\Domain;

use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Shared\Domain\AggregateRoot;
use Modules\Shared\Domain\Text;

final class Invoice extends AggregateRoot
{
    private Price $totalPrice;

    private function __construct(
        private InvoiceId $id,
        private StatusEnum $status,
        private Text $customerName,
        private Text $customerEmail,
        private InvoiceLines $lines,
    ) {
        $this->totalPrice = $lines->getTotalPrice();
    }

    public static function create(InvoiceId $id, Text $customerName, Text $customerEmail): self {
        return new self(
            id: $id,
            status: StatusEnum::Draft,
            customerName: $customerName,
            customerEmail: $customerEmail,
            lines: InvoiceLines::create(),
        );
    }

    public static function restore(
        InvoiceId $id,
        StatusEnum $status,
        Text $customerName,
        Text $customerEmail,
        InvoiceLines $lines,
    ): self {
        return new self(
            id: $id,
            status: $status,
            customerName: $customerName,
            customerEmail: $customerEmail,
            lines: $lines,
        );
    }

    public function send(): void
    {
        if ($this->status !== StatusEnum::Draft) {
            throw new CannotBeSentException("Only Draft invoices can be sent.");
        }

        if (!$this->isFilled()) {
            throw new CannotBeSentException("Invoice without lines cannot be sent.");
        }

        $this->status = StatusEnum::Sending;
    }

    public function confirmDelivery(): void
    {
        if ($this->status !== StatusEnum::Sending) {
            throw new CannotConfirmDeliveryException("Invoice must be sent before confirming delivery");
        }

        $this->status = StatusEnum::SentToClient;
    }

    public function getId(): InvoiceId
    {
        return $this->id;
    }

    public function getStatus(): StatusEnum
    {
        return $this->status;
    }

    public function getCustomerName(): Text
    {
        return $this->customerName;
    }

    public function getCustomerEmail(): Text
    {
        return $this->customerEmail;
    }

    public function getLines(): InvoiceLines
    {
        return $this->lines;
    }

    public function addLine(InvoiceLine $line): self
    {
        $this->lines->addLine($line);

        return $this;
    }

    public function getTotalPrice(): Price
    {
        return $this->totalPrice;
    }

    public function isFilled(): bool
    {
        if ($this->lines->isEmpty()) {
            return false;
        }

        if ($this->lines->hasUnfilledLines()) {
            return false;
        }

        return true;
    }
}
