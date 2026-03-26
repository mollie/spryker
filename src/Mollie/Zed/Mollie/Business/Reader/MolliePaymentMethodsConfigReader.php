<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Reader;

use Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer;
use Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface;

class MolliePaymentMethodsConfigReader implements MolliePaymentMethodsConfigReaderInterface
{
    public function __construct(
        private MollieRepositoryInterface $repository,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer $molliePaymentMethodConfigCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer
     */
    public function getPaymentMethodConfigCollection(
        MolliePaymentMethodConfigCriteriaTransfer $molliePaymentMethodConfigCriteriaTransfer,
    ): MolliePaymentMethodConfigCollectionTransfer {
        return $this->repository->getPaymentMethodConfigCollection($molliePaymentMethodConfigCriteriaTransfer);
    }

    /**
     * @param string $key
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer|null
     */
    public function getPaymentMethodConfigByMollieKey(string $key): ?MolliePaymentMethodConfigTransfer
    {
        return $this->repository->getMolliePaymentMethodConfigByMollieKey($key);
    }
}
