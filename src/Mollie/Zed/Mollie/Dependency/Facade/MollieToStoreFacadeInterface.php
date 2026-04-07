<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Dependency\Facade;

interface MollieToStoreFacadeInterface
{
    /**
     * Specification:
     * - Reads all active stores and returns list of transfers.
     * - Executes stack of {@link \Spryker\Zed\StoreExtension\Dependency\Plugin\StoreCollectionExpanderPluginInterface} plugins.
     *
     * @api
     *
     * @return array<\Generated\Shared\Transfer\StoreTransfer>
     */
    public function getAllStores(): array;
}
