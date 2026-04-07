<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Dependency\Facade;

use Spryker\Zed\Store\Business\StoreFacadeInterface;

class MollieToStoreFacadeBridge implements MollieToStoreFacadeInterface
{
    /**
     * @param \Spryker\Zed\Store\Business\StoreFacadeInterface $storeFacade
     */
    public function __construct(private StoreFacadeInterface $storeFacade)
    {
    }

    /**
     * Specification:
     * - Reads all active stores and returns list of transfers.
     * - Executes stack of {@link \Spryker\Zed\StoreExtension\Dependency\Plugin\StoreCollectionExpanderPluginInterface} plugins.
     *
     * @api
     *
     * @return array<\Generated\Shared\Transfer\StoreTransfer>
     */
    public function getAllStores(): array
    {
        return $this->storeFacade->getAllStores();
    }
}
