<?php

namespace Tests\Unit\Invoice\Domain\Unit\Invoice\Domain;

use Modules\Invoices\Domain\Quantity;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Modules\Invoices\Domain\NegativeQuantityException;

class QuantityTest extends TestCase
{
    #[DataProvider('quantityProvider')]
    #[Test]
    public function it_can_be_created(float $value, ?string $expectedException): void
    {
        if ($expectedException !== null) {
            $this->expectException($expectedException);
        }

        $quantity = Quantity::create($value);

        if ($expectedException === null) {
            $this->assertInstanceOf(Quantity::class, $quantity);
            $this->assertEquals($value, $quantity->toFloat());
        }
    }

    /**
     * @return array
     */
    public static function quantityProvider(): array
    {
        return [
            [10.0, null],
            [0.0, null],
            [-1.0, NegativeQuantityException::class],
        ];
    }

    #[DataProvider('isPositiveQuantityProvider')]
    #[Test]
    public function itChecksIfIsPositive(float $value, bool $expectedIsPositiveResult): void
    {
        $quantity = Quantity::create($value);

        $this->assertSame($expectedIsPositiveResult, $quantity->isPositive());
    }

    /**
     * @return array
     */
    public static function isPositiveQuantityProvider(): array
    {
        return [
            [10.0, true],
            [0.0, false],
        ];
    }
}
