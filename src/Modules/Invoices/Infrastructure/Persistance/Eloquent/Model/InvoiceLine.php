<?php

namespace Modules\Invoices\Infrastructure\Persistance\Eloquent\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceLine extends Model
{
    use HasUuids;

    protected $table = "invoice_product_lines";

    public $timestamps = true;

    protected $fillable = [
        'name',
        'quantity',
        'price',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function price(): Attribute
    {
        return Attribute::make(
            get: fn(int $value) => $value / 100,
            set: fn(float $value) => $value * 100,
        );
    }

    public function quantity(): Attribute
    {
        return Attribute::make(
            get: fn(int $value) => $value / 100,
            set: fn(float $value) => $value * 100,
        );
    }
}
