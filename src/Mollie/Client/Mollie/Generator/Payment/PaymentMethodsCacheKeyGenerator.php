<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie\Generator\Payment;

use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Mollie\Client\Mollie\MollieConfig;

class PaymentMethodsCacheKeyGenerator implements PaymentMethodsCacheKeyGeneratorInterface
{
    protected const string TEST_MODE_IDENTIFIER = 'test';

    protected const string LIVE_MODE_IDENTIFIER = 'live';

    protected const string ISSUERS_INCLUDED = 'issuers-included';

    protected const string ISSUERS_EXCLUDED = 'issuers-excluded';

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
        $includeIssuers = $queryParametersTransfer->getIncludeIssuers() ? static::ISSUERS_INCLUDED : static::ISSUERS_EXCLUDED;
        $profileId = $this->config->getMollieProfileId();
        $amount = $queryParametersTransfer->getAmount();

        $keyParts = [
            $cacheKeyPrefix,
            $profileId,
            $mode,
            $includeIssuers,
            $queryParametersTransfer->getSequenceType(),
            $queryParametersTransfer->getLocale(),
            $amount?->getCurrency(),
            $amount?->getValue(),
            $queryParametersTransfer->getBillingCountry(),
        ];

        return implode(':', array_filter($keyParts));
    }
}
