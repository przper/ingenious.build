<?php

namespace Modules\Shared\Domain;

final readonly class Text implements \Stringable
{
    private function __construct(
        private string $value,
    ) {
    }

    public static function create(string $value): self
    {
        return new self($value);
    }

    public function __toString()
    {
        return $this->value;
    }
}
