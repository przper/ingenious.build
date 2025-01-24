<?php

namespace Modules\Shared\Domain;

use Ramsey\Uuid\Uuid as RamseyUuid;

abstract readonly class Uuid implements \Stringable
{
    public function __construct(
        public string $value,
    ) {
        $this->guard();
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private function guard(): void
    {
        if (!RamseyUuid::isValid($this->value)) {
            throw new IncorrectUuidException();
        }
    }
}
