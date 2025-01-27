<?php

namespace Modules\Invoices\Presentation\Http;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\JsonResponse;
use Modules\Invoices\Api\InvoiceFacadeInterface;

final readonly class GetInvoiceController
{
    public function __construct(
        private InvoiceFacadeInterface $invoices,
        private Dispatcher $dispatcher,
    ) {
    }

    public function __invoke(string $id): JsonResponse
    {
        $invoice = $this->invoices->get($id);

        if ($invoice === null) {
            abort(404);
        }

        return new JsonResponse(get_object_vars($invoice));
    }
}
