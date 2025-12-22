<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Mapper;

use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

interface MollieApiResponseMapperInterface
{
    /**
     * @param array<string, mixed> $payload
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    public function mapPayloadToResponseTransfer(array $payload): AbstractTransfer;

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function mapDataToArray(mixed $data): mixed;
}
