<?php

namespace Tests\MotherObjects\Invoice\Domain;

use Illuminate\Foundation\Testing\WithFaker;
use Modules\Invoices\Domain\InvoiceLine;
use Modules\Invoices\Domain\InvoiceLineId;
use Modules\Invoices\Domain\Price;
use Modules\Invoices\Domain\Quantity;
use Modules\Shared\Domain\Service\UuidService;
use Modules\Shared\Domain\Text;

class InvoiceLineMother
{
    use WithFaker;

    private Text $productName;
    private Quantity $quantity;
    private Price $unitPrice;

    public function __construct()
    {
        $this->setUpFaker();

        $this->productName = Text::create($this->faker->words(3, true));
        $this->quantity = Quantity::create($this->faker->randomFloat(3, min: 0.01, max: 100));
        $this->unitPrice = Price::create($this->faker->randomFloat(2, min: 0.01, max: 100));
    }

    public static function init(): self
    {
        return new self();
    }

    public function unitPrice(float $price): self
    {
        $this->unitPrice = Price::create($price);

        return $this;
    }

    public function quantity(float $quantity): self
    {
        $this->quantity = Quantity::create($quantity);

        return $this;
    }

    public function build(): InvoiceLine
    {
        return InvoiceLine::create(
            id: new InvoiceLineId(UuidService::generateNew()),
            productName: $this->productName,
            quantity: $this->quantity,
            unitPrice: $this->unitPrice,
        );
    }
}
