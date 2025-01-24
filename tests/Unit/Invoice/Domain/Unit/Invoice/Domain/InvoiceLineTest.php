<?php

namespace Tests\Unit\Invoice\Domain\Unit\Invoice\Domain;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\MotherObjects\Invoice\Domain\InvoiceLineMother;

class InvoiceLineTest extends TestCase
{
    #[DataProvider('invoiceLineData')]
    #[Test]
    public function it_checks_if_line_is_filled(float $unitPrice, float $quantity, bool $expectedResult): void
    {
        $invoiceLine = InvoiceLineMother::init()->unitPrice($unitPrice)->quantity($quantity)->build();

        $this->assertSame($expectedResult, $invoiceLine->isFilled());
    }

    public static function invoiceLineData(): array
    {
        return [
            [1.0, 1.0, true],  // Test case with positive unit price and quantity.
            [0.0, 1.0, false], // Test case with zero unit price.
            [1.0, 0.0, false]  // Test case with zero quantity.
        ];
    }
}
