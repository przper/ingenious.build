<?php

namespace Modules\Invoices\Infrastructure\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Modules\Invoices\Application\Facade\InvoiceFacade;
use Modules\Invoices\Application\Facade\InvoiceFacadeInterface;
use Modules\Invoices\Domain\InvoiceRepositoryInterface;
use Modules\Invoices\Infrastructure\Persistance\Eloquent\InvoiceRepository;
use Modules\Notifications\Infrastructure\Drivers\DummyDriver;

final class InvoiceServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public $bindings = [
        InvoiceFacadeInterface::class => InvoiceFacade::class,
        InvoiceRepositoryInterface::class => InvoiceRepository::class,
    ];

    public function register(): void
    {
//        $this->app->scoped(InvoiceFacadeInterface::class, InvoiceFacade::class);
//
//        $this->app->singleton(InvoiceFacade::class, static fn($app) => new InvoiceFacade(
//            invoiceRepository: $app->make(InvoiceRepositoryInterface::class),
//            notifications: $app->make(DummyDriver::class),
//        ));
    }

    /** @return array<class-string> */
    public function provides(): array
    {
        return [
            InvoiceFacadeInterface::class,
        ];
    }
}
