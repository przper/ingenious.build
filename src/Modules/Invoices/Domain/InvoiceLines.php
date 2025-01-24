<?php

namespace Modules\Invoices\Domain;

use Modules\Invoices\Domain\Service\PriceService;
use Modules\Shared\Domain\Collection;

/** @extends Collection<InvoiceLine> */
final class InvoiceLines extends Collection
{
    private function __construct(
        /** @var InvoiceLine[] $lines */
        private array $lines = [],
    ) {
    }

    public static function create(): self
    {
        return new self();
    }

    public function addLine(InvoiceLine $line): self
    {
        $this->lines[] = $line;

        return $this;
    }

    public function getTotalPrice(): Price
    {
        $price = Price::create(0.0);

        foreach ($this->lines as $line) {
            $price = PriceService::sum($price, $line->getTotalPrice());
        }

        return $price;
    }

    public function isEmpty(): bool
    {
        return count($this->lines) === 0;
    }

    public function hasUnfilledLines(): bool
    {
        foreach ($this->lines as $line) {
            if (!$line->isFilled()) {
                return true;
            }
        }

        return false;
    }

    protected function getItems(): array
    {
        return $this->lines;
    }
}
