<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Cache;

use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Zed\Mollie\Communication\Mapper\MollieCommunicationMapperInterface;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToLocaleFacadeInterface;

class MollieCacheInvalidator implements MollieCacheInvalidatorInterface
{
    /**
     * @param \Mollie\Zed\Mollie\Communication\Mapper\MollieCommunicationMapperInterface $mapper
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     * @param \Mollie\Zed\Mollie\Dependency\Facade\MollieToLocaleFacadeInterface $localeFacade
     */
    public function __construct(
        protected MollieCommunicationMapperInterface $mapper,
        protected MollieClientInterface $mollieClient,
        protected MollieToLocaleFacadeInterface $localeFacade,
    ) {
    }

    /**
     * @return void
     */
    public function invalidateCache(): void
    {
        $locale = $this->localeFacade->getCurrentLocale()->getLocaleName();
        $transfer = $this->mapper->createMolliePaymentMethodQueryParametersTransfer($locale, null);

        $this->mollieClient->deleteEnabledPaymentMethodsCache($transfer);
        $this->mollieClient->deleteAllPaymentMethodsCache($transfer);
    }
}
