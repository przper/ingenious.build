<?php

namespace Modules\Shared\Domain\Service;

use Ramsey\Uuid\Uuid;

final readonly class UuidService
{
    public static function generateNew(): string
    {
        return Uuid::uuid4()->toString();
    }
}
