<?php

namespace Modules\Invoices\Domain;

final readonly class Price
{
    private function __construct(
        private float $value,
    ) {
    }

    /**
     * @throws NegativePriceException
     */
    public static function create(float $value): self
    {
        $price = new self($value);

        $price->guard();

        return $price;
    }

    public function isPositive(): bool
    {
        return $this->value > 0.0;
    }

    public function toFloat(): float
    {
        return $this->value;
    }

    /** @throws NegativePriceException */
    private function guard(): void
    {
        if ($this->value < 0) {
            throw new NegativePriceException();
        }
    }
}
