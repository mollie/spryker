<?php

namespace Mollie\Zed\Mollie\Dependency\Facade;

use Propel\Runtime\Collection\ObjectCollection;

interface MollieToOmsInterface
{
    /**
     * @param string $eventId
     * @param \Propel\Runtime\Collection\ObjectCollection $orderItemIds
     * @param array<string, mixed> $data
     *
     * @return array<int, mixed>
     */
    public function triggerEvent(string $eventId, ObjectCollection $orderItemIds, array $data): array;
}
