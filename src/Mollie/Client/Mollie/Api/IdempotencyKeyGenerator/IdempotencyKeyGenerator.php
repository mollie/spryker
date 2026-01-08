<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Api\IdempotencyKeyGenerator;

use Mollie\Api\Contracts\IdempotencyKeyGeneratorContract;
use Ramsey\Uuid\Uuid;

class IdempotencyKeyGenerator implements IdempotencyKeyGeneratorContract
{
    /**
     * @return string
     */
    public function generate(): string
    {
        return Uuid::uuid4()->toString();
    }
}
