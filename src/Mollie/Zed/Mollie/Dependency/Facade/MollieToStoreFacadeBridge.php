<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Dependency\Facade;

use Spryker\Zed\Store\Business\StoreFacade;
use Spryker\Zed\Store\Business\StoreFacadeInterface;

class MollieToStoreFacadeBridge implements MollieToStoreFacadeInterface
{
    public function __construct(StoreFacadeInterface $storeFacade)
    {
        $this->storeFacade = $storeFacade;
    }

    public function getAllStores()
    {
        return $this->storeFacade->getAllStores();
    }
}
