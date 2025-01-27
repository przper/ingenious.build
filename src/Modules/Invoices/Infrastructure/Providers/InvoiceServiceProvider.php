<?php

namespace Modules\Invoices\Infrastructure\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Modules\Invoices\Api\InvoiceFacadeInterface;
use Modules\Invoices\Application\Facades\InvoiceFacade;
use Modules\Invoices\Application\Listeners\InvoiceDeliveredListener;
use Modules\Invoices\Domain\InvoiceRepositoryInterface;
use Modules\Invoices\Infrastructure\Persistance\Eloquent\InvoiceRepository;
use Modules\Notifications\Api\Events\ResourceDeliveredEvent;

final class InvoiceServiceProvider extends ServiceProvider
{
    public $singletons = [
        InvoiceFacadeInterface::class => InvoiceFacade::class,
        InvoiceRepositoryInterface::class => InvoiceRepository::class,
    ];

    public function register(): void
    {
        Event::listen(ResourceDeliveredEvent::class, InvoiceDeliveredListener::class);
    }
}
