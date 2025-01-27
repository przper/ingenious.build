<?php

namespace Modules\Invoices\Application\Listeners;

use Modules\Invoices\Application\Facade\InvoiceFacadeInterface;
use Modules\Notifications\Api\Events\ResourceDeliveredEvent;

class InvoiceDeliveredListener
{
    public function __construct(
        private InvoiceFacadeInterface $invoices,
    ) {
    }

    public function handle(ResourceDeliveredEvent $event): void
    {
        $invoice = $this->invoices->get($event->resourceId->toString());

        if ($invoice === null) {
            return;
        }

        $this->invoices->confirmDelivery($invoice->id);
    }
}
