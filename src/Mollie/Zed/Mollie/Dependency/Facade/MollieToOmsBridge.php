<?php

namespace Mollie\Zed\Mollie\Dependency\Facade;

use Propel\Runtime\Collection\ObjectCollection;

class MollieToOmsBridge implements MollieToOmsInterface
{
    /**
     * @var \Spryker\Zed\Oms\Business\OmsFacadeInterface
     */
    protected $omsFacade;

    /**
     * @param \Spryker\Zed\Oms\Business\OmsFacadeInterface $omsFacade
     */
    public function __construct($omsFacade)
    {
        $this->omsFacade = $omsFacade;
    }

    /**
     * @param string $eventId
     * @param \Propel\Runtime\Collection\ObjectCollection $orderItemIds
     * @param array<string, mixed> $data
     *
     * @return array<int, mixed>
     */
    public function triggerEvent(string $eventId, ObjectCollection $orderItemIds, array $data): array
    {
        return $this->omsFacade->triggerEvent($eventId, $orderItemIds, $data);
    }
}
