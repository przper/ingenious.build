<?php

namespace Tests\Unit\Invoice\Domain;

use Modules\Invoices\Domain\NegativePriceException;
use Modules\Invoices\Domain\Price;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PriceTest extends TestCase
{
    #[DataProvider('priceProvider')]
    #[Test]
    public function it_can_be_created(float $value, ?string $expectedException): void
    {
        if ($expectedException !== null) {
            $this->expectException($expectedException);
        }

        $price = Price::create($value);

        if ($expectedException === null) {
            $this->assertInstanceOf(Price::class, $price);
            $this->assertEquals($value, $price->toFloat());
        }
    }

    /**
     * @return array
     */
    public static function priceProvider(): array
    {
        return [
            [10.0, null],
            [0.0, null],
            [-1.0, NegativePriceException::class],
        ];
    }

    #[DataProvider('isPositivePriceProvider')]
    #[Test]
    public function itChecksIfIsPositive(float $value, bool $expectedIsPositiveResult): void
    {
        $price = Price::create($value);

        $this->assertSame($expectedIsPositiveResult, $price->isPositive());
    }

    /**
     * @return array
     */
    public static function isPositivePriceProvider(): array
    {
        return [
            [10.0, true],
            [0.0, false],
        ];
    }
}
