<?php

namespace Tests\Unit\Invoice\Domain;

use Modules\Invoices\Domain\Price;
use Modules\Invoices\Domain\Quantity;
use Modules\Invoices\Domain\Service\PriceService;
use Tests\TestCase;

class PriceServiceTest extends TestCase
{
    public function test_it_sums_prices(): void
    {
        $a = Price::create(111.39);
        $b = Price::create(432.32);

        $this->assertEquals(Price::create(543.71), PriceService::sum($a, $b));
    }

    public function test_it_calculates_total_price(): void
    {
        $this->assertEquals(Price::create(9.54), PriceService::calculateTotalPrice(Quantity::create(3.19), Price::create(2.99)));
    }
}
