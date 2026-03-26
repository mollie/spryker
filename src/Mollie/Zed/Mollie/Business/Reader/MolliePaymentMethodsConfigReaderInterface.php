<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Reader;

use Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer;

interface MolliePaymentMethodsConfigReaderInterface
{
    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer $molliePaymentMethodConfigCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer
     */
    public function getPaymentMethodConfigCollection(
        MolliePaymentMethodConfigCriteriaTransfer $molliePaymentMethodConfigCriteriaTransfer,
    ): MolliePaymentMethodConfigCollectionTransfer;

    public function getPaymentMethodConfigByMollieKey(string $key): ?MolliePaymentMethodConfigTransfer;
}
