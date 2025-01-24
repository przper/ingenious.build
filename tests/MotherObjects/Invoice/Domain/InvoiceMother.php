<?php

namespace Tests\MotherObjects\Invoice\Domain;

use Illuminate\Foundation\Testing\WithFaker;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\Invoice;
use Modules\Invoices\Domain\InvoiceId;
use Modules\Invoices\Domain\InvoiceLine;
use Modules\Invoices\Domain\InvoiceLines;
use Modules\Shared\Domain\Service\UuidService;
use Modules\Shared\Domain\Text;

class InvoiceMother
{
    use WithFaker;

    private StatusEnum $status;
    private Text $customerName;
    private Text $customerEmail;
    private InvoiceLines $lines;

    public function __construct()
    {
        $this->setUpFaker();

        $this->status = StatusEnum::Draft;
        $this->customerName = Text::create($this->faker->company());
        $this->customerEmail = Text::create($this->faker->companyEmail());
        $this->lines = InvoiceLines::create();
    }

    public static function init(): self
    {
        return new self();
    }

    public function status(StatusEnum $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function lines(InvoiceLines $lines): self
    {
        foreach ($lines as $line) {
            $this->lines->addLine($line);
        }

        return $this;
    }

    public function addLine(InvoiceLine $line): self
    {
        $this->lines->addLine($line);

        return $this;
    }

    public function build(): Invoice
    {
        return Invoice::restore(
            id: new InvoiceId(UuidService::generateNew()),
            status: $this->status,
            customerName: $this->customerName,
            customerEmail: $this->customerEmail,
            lines: $this->lines,
        );
    }
}
