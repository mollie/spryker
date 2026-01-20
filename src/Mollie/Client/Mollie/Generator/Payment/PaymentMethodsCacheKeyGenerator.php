<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie\Generator\Payment;

use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Mollie\Client\Mollie\MollieConfig;

class PaymentMethodsCacheKeyGenerator implements PaymentMethodsCacheKeyGeneratorInterface
{
    protected const string TEST_MODE_IDENTIFIER = 'test';

    protected const string LIVE_MODE_IDENTIFIER = 'live';

    /**
     * @param \Mollie\Client\Mollie\MollieConfig $config
     */
    public function __construct(protected MollieConfig $config)
    {
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer $queryParametersTransfer
     * @param string $cacheKeyPrefix
     *
     * @return string
     */
    public function generateCacheKey(
        MolliePaymentMethodQueryParametersTransfer $queryParametersTransfer,
        string $cacheKeyPrefix,
    ): string {
        $mode = $this->config->isMollieTestModeEnabled() ? static::TEST_MODE_IDENTIFIER : static::LIVE_MODE_IDENTIFIER;

        $profileId = $queryParametersTransfer->getProfileId();
        $amount = $queryParametersTransfer->getAmount();
        $keyParts = [
            $cacheKeyPrefix,
            $queryParametersTransfer->getStoreName(),
            $profileId,
            $mode,
            $queryParametersTransfer->getLocale(),
            $amount?->getCurrency(),
            $amount?->getValue(),
            $queryParametersTransfer->getBillingCountry(),
            $queryParametersTransfer->getSequenceType(),
        ];

        return implode(':', array_filter($keyParts));
    }
}
