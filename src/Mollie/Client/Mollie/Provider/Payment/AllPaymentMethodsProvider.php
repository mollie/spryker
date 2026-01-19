<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie\Provider\Payment;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieAvailablePaymentMethodsApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer;
use Mollie\Client\Mollie\Api\ApiCallInterface;
use Mollie\Client\Mollie\Dependency\Client\MollieToLocaleClientInterface;
use Mollie\Client\Mollie\Dependency\Client\MollieToStorageClientInterface;
use Mollie\Client\Mollie\Dependency\Client\MollieToStoreClientInterface;
use Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Mollie\Client\Mollie\Mapper\Payment\PaymentMethodsMapperInterface;
use Mollie\Client\Mollie\MollieConfig;
use Mollie\Shared\Mollie\MollieConstants;

class AllPaymentMethodsProvider extends AbstractPaymentMethodsProvider implements PaymentMethodsProviderInterface
{
    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return string
     */
    protected function generateCacheKey(MollieApiRequestTransfer $mollieApiRequestTransfer): string
    {
//        $locale = $this->localeClient->getCurrentLocale();
        $locale = 'de_DE';
//        $storeName = $this->storeClient->getCurrentStore()->getName();
        $storeName = 'DE';
        $mode = $this->config->getMollieTestModeEnabled();
        $currency = 'null';
        $value = 'null';
        
        $queryParametersTransfer = $mollieApiRequestTransfer->getMolliePaymentMethodQueryParameters();
        $profileId = $queryParametersTransfer->getProfileId() ?: 'null';
        $includeIssuers = $queryParametersTransfer->getIncludeIssuers() ? 1 : 0;
        $includePricing = $queryParametersTransfer->getIncludePricing() ? 1 : 0;

        $amountTransfer = $queryParametersTransfer->getAmount();
        if ($amountTransfer) {
            $currency = $amountTransfer->getCurrency() ?: 'null';
            $value = $amountTransfer->getValue() ?: 'null';
        }

        $key = MollieConstants::MOLLIE_STORAGE_ALL_PAYMENT_METHODS_KEY
        . static::DELIMITER
        . $storeName
        . static::DELIMITER
        . $mode
        . static::DELIMITER
        . $locale
        . static::DELIMITER
        . $currency
        . static::DELIMITER
        . $value
        . static::DELIMITER
        . $includeIssuers
        . static::DELIMITER
        . $includePricing
        . static::DELIMITER
        . $profileId;

        return $key;
    }
}
