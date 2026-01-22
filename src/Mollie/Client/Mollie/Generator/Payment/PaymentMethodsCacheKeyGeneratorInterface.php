<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie\Generator\Payment;

use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;

interface PaymentMethodsCacheKeyGeneratorInterface
{
    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer $queryParametersTransfer
     * @param string $cacheKeyPrefix
     *
     * @return string
     */
    public function generateCacheKey(
        MolliePaymentMethodQueryParametersTransfer $queryParametersTransfer,
        string $cacheKeyPrefix,
    ): string;
}
