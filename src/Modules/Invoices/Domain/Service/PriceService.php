<?php

namespace Modules\Invoices\Domain\Service;

use Modules\Invoices\Domain\Price;
use Modules\Invoices\Domain\Quantity;

final readonly class PriceService
{
    public static function sum(Price $a, Price $b): Price
    {
        $sum = round($a->toFloat() + $b->toFloat(), 2);

        return Price::create($sum);
    }

    public static function calculateTotalPrice(Quantity $quantity, Price $unitPrice): Price
    {
        $total = round($quantity->toFloat() * $unitPrice->toFloat(), 2);

        return Price::create($total);
    }
}
