<?php

namespace Modules\Invoices\Domain;

final readonly class Quantity
{
    private function __construct(
        private float $value,
    ) {
    }

    /**
     * @throws NegativeQuantityException
     */
    public static function create(float $value): self
    {
        $quantity = new self($value);

        $quantity->guard();

        return $quantity;
    }

    public function isPositive(): bool
    {
        return $this->value > 0.0;
    }

    public function toFloat(): float
    {
        return $this->value;
    }

    /**
     * @throws NegativeQuantityException
     */
    private function guard(): void
    {
        if ($this->value < 0) {
            throw new NegativeQuantityException();
        }
    }
}
