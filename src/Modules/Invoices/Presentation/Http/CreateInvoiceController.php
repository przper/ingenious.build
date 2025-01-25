<?php

namespace Modules\Invoices\Presentation\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Invoices\Application\Dto\InvoiceLineCreateData;
use Modules\Invoices\Application\Facade\InvoiceFacadeInterface;

final readonly class CreateInvoiceController
{
    public function __construct(
        private InvoiceFacadeInterface $invoices,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_name' => 'required|string',
            'customer_email' => 'required|string',
            'lines' => 'array',
            'lines.*.product_name' => 'required|string',
            'lines.*.quantity' => 'required|numeric',
            'lines.*.unit_price' => 'required|numeric',
        ]);

        $lines = array_map(fn(array $input) => new InvoiceLineCreateData(
            productName: $input['product_name'],
            unitPrice: $input['unit_price'],
            quantity: $input['quantity'],
        ), $validated['lines'] ?? []);

        $invoiceId = $this->invoices->create(
            customerName: $validated['customer_name'],
            customerEmail: $validated['customer_email'],
            lines: $lines,
        );

        return new JsonResponse(['id' => $invoiceId]);
    }
}
