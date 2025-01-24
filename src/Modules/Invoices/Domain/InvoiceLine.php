<?php

namespace Modules\Invoices\Domain;

use Modules\Invoices\Domain\Service\PriceService;
use Modules\Shared\Domain\Text;
use Modules\Shared\Domain\Uuid;

final class InvoiceLine
{
    private Price $totalPrice;

    private function __construct(
        private InvoiceLineId $id,
        private Text $productName,
        private Quantity $quantity,
        private Price $unitPrice,
    ) {
        $this->totalPrice = PriceService::calculateTotalPrice($quantity, $unitPrice);
    }

    public static function create(
        InvoiceLineId $id,
        Text $productName,
        Quantity $quantity,
        Price $unitPrice,
    ): self {
        return new self(
            id: $id,
            productName: $productName,
            quantity: $quantity,
            unitPrice: $unitPrice,
        );
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getProductName(): Text
    {
        return $this->productName;
    }

    public function setProductName(Text $productName): void
    {
        $this->productName = $productName;
    }

    public function getQuantity(): Quantity
    {
        return $this->quantity;
    }

    public function setQuantity(Quantity $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getUnitPrice(): Price
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(Price $unitPrice): void
    {
        $this->unitPrice = $unitPrice;
    }

    public function getTotalPrice(): Price
    {
        return $this->totalPrice;
    }

    public function isFilled(): bool
    {
        if (!$this->unitPrice->isPositive()) {
            return false;
        }

        if (!$this->quantity->isPositive()) {
            return false;
        }

        return true;
    }
}
