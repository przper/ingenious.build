<?php

namespace Tests\Unit\Invoice\Domain;

use Modules\Invoices\Domain\CannotBeSentException;
use Modules\Invoices\Domain\CannotConfirmDeliveryException;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\InvoiceLines;
use Modules\Invoices\Domain\Price;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
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

    #[DataProvider('sendWrongStatuses')]
    #[Test]
    public function it_should_not_send_invoice_with_not_filled_lines(StatusEnum $status): void
    {
        $invoice = InvoiceMother::init()
            ->status(StatusEnum::Draft)
            ->lines(InvoiceLines::create())
            ->build();

        $this->expectException(CannotBeSentException::class);
        $this->expectExceptionMessage("Invoice without lines cannot be sent.");

        $invoice->send();
    }

    public static function sendWrongStatuses(): array
    {
        return [
            'Sending status' => [StatusEnum::Sending],
            'Sent to client status' => [StatusEnum::SentToClient],
        ];
    }
    #[DataProvider('confirmDeliveryProvider')]
    #[Test]
    public function testConfirmDelivery(StatusEnum $status, bool $shouldThrowException): void
    {
        $invoice = InvoiceMother::init()
            ->status($status)
            ->addLine(InvoiceLineMother::init()->unitPrice(59.99)->quantity(1)->build())
            ->build();

        if ($shouldThrowException) {
            $this->expectException(CannotConfirmDeliveryException::class);
            $this->expectExceptionMessage("Invoice must be sent before confirming delivery");
        }

        $invoice->confirmDelivery();

        if (!$shouldThrowException) {
            $this->assertEquals(StatusEnum::SentToClient, $invoice->getStatus());
        }
    }

    public static function confirmDeliveryProvider(): iterable
    {
        yield [StatusEnum::Draft, true];
        yield [StatusEnum::Sending, false];
        yield [StatusEnum::SentToClient, true];
    }
}
