<?php

namespace Modules\Invoices\Infrastructure\Persistance\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Invoices\Domain\Enums\StatusEnum;

class Invoice extends Model
{
    use HasUuids;

    public $timestamps = true;

    protected $casts = [
        'status' => StatusEnum::class,
    ];

    public function lines(): HasMany
    {
        return $this->hasMany(InvoiceLine::class);
    }
}
