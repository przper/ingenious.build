<?php

namespace Tests\Unit\Invoice\Domain\Unit\Invoice\Domain;

use Modules\Invoices\Domain\CannotBeSentException;
use Modules\Invoices\Domain\Enums\StatusEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Modules\Invoices\Domain\InvoiceLines;
use Modules\Invoices\Domain\Price;
use Tests\MotherObjects\Invoice\Domain\InvoiceLineMother;
use Tests\MotherObjects\Invoice\Domain\InvoiceMother;

class InvoiceTest extends TestCase
{
    #[Test]
    public function get_total_price(): void
    {
        $invoice = InvoiceMother::init()
            ->addLine(InvoiceLineMother::init()->unitPrice(59.99)->quantity(3.21)->build())
            ->addLine(InvoiceLineMother::init()->unitPrice(299.99)->quantity(0.178)->build())
            ->build();

        $this->assertEquals(Price::create(245.97), $invoice->getTotalPrice());
    }

    #[DataProvider('isFilledProvider')]
    #[Test]
    public function is_filled(InvoiceLines $invoiceLines, bool $expected): void
    {
        $invoice = InvoiceMother::init()->lines($invoiceLines)->build();

        $this->assertEquals($expected, $invoice->isFilled());
    }

    public static function isFilledProvider(): iterable
    {
        yield 'all filled' => [
            InvoiceLines::create()
                ->addLine(InvoiceLineMother::init()->unitPrice(0.0)->quantity(3.0)->build())
                ->addLine(InvoiceLineMother::init()->unitPrice(299.99)->quantity(0.0)->build()),
            false,
        ];

        yield 'no lines' => [
            InvoiceLines::create(),
            false,
        ];

        yield 'has not filled line' => [
            InvoiceLines::create()
                ->addLine(InvoiceLineMother::init()->unitPrice(59.99)->quantity(3.21)->build())
                ->addLine(InvoiceLineMother::init()->unitPrice(299.99)->quantity(0.178)->build()),
            true,
        ];
    }

    #[Test]
    public function invoice_can_be_sent(): void
    {
        $invoice = InvoiceMother::init()
            ->status(StatusEnum::Draft)
            ->addLine(InvoiceLineMother::init()->unitPrice(59.99)->quantity(3.21)->build())
            ->addLine(InvoiceLineMother::init()->unitPrice(299.99)->quantity(0.178)->build())
            ->build();

        $invoice->send();
        $this->assertEquals(StatusEnum::Sending, $invoice->getStatus());
    }
    #[Test]
    public function it_should_not_send_invoice_with_not_filled_lines(): void
    {
        $invoice = InvoiceMother::init()
            ->status(StatusEnum::Draft)
            ->lines(InvoiceLines::create())
            ->build();

        $this->expectException(CannotBeSentException::class);
        $this->expectExceptionMessage("Invoice without lines cannot be sent.");

        $invoice->send();
    }
    #[Test]
    public function it_should_not_send_invoice_with_status_not_draft(): void
    {
        $invoice = InvoiceMother::init()
            ->status(StatusEnum::SentToClient)
            ->addLine(InvoiceLineMother::init()->unitPrice(59.99)->quantity(3.21)->build())
            ->addLine(InvoiceLineMother::init()->unitPrice(299.99)->quantity(0.178)->build())
            ->build();

        $this->expectException(CannotBeSentException::class);
        $this->expectExceptionMessage("Only Draft invoices can be sent.");

        $invoice->send();
    }
}
