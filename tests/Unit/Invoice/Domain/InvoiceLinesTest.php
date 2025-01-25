<?php

namespace Tests\Unit\Invoice\Domain;

use Modules\Invoices\Domain\InvoiceLines;
use Modules\Invoices\Domain\Price;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\MotherObjects\Invoice\Domain\InvoiceLineMother;

class InvoiceLinesTest extends TestCase
{
    public function test_it_detects_if_it_is_empty(): void
    {
        $invoiceLines = InvoiceLines::create();

        $this->assertTrue($invoiceLines->isEmpty());

        $invoiceLines->addLine(InvoiceLineMother::init()->build());

        $this->assertFalse($invoiceLines->isEmpty());
    }

    #[DataProvider('getTotalPriceProvider')]
    #[Test]
    public function get_total_price(InvoiceLines $invoiceLines, Price $expectedTotalPrice): void
    {
        $this->assertEquals($expectedTotalPrice, $invoiceLines->getTotalPrice());
    }

    public static function getTotalPriceProvider(): iterable
    {
        $invoiceLine1 = InvoiceLineMother::init()->unitPrice(50.0)->quantity(2.0)->build();
        $invoiceLine2 = InvoiceLineMother::init()->unitPrice(30.0)->quantity(3.0)->build();

        yield 'total price for empty invoice lines' => [
            InvoiceLines::create()->addLine($invoiceLine1),
            Price::create(100.0),
        ];

        yield 'total price for invoice lines with multiple items' => [
            InvoiceLines::create()->addLine($invoiceLine1)->addLine($invoiceLine2),
            Price::create(190.0),
        ];
    }

    #[DataProvider('getInvoiceLinesProvider')]
    #[Test]
    public function has_unfilled_lines(InvoiceLines $invoiceLines, bool $expectedResult): void
    {
        $this->assertEquals($expectedResult, $invoiceLines->hasUnfilledLines());
    }

    public static function getInvoiceLinesProvider(): iterable
    {
        $filledInvoiceLine = InvoiceLineMother::init()->build();
        $unfilledInvoiceLine = InvoiceLineMother::init()->unitPrice(0.0)->build();

        yield 'all lines filled' => [
            InvoiceLines::create()->addLine($filledInvoiceLine),
            false,
        ];

        yield 'some lines unfilled' => [
            InvoiceLines::create()->addLine($filledInvoiceLine)->addLine($unfilledInvoiceLine),
            true,
        ];
    }
}
