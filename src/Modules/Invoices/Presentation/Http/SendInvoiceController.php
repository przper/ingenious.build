<?php

namespace Modules\Invoices\Presentation\Http;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Invoices\Application\Facade\InvoiceFacadeInterface;

final readonly class SendInvoiceController
{
    public function __construct(
        private InvoiceFacadeInterface $invoices,
    ) {
    }

    public function __invoke(string $id, Request $request): Response
    {
        $this->invoices->send($id, $request->get('subject'), $request->get('email'));

        return response()->noContent();
    }
}
