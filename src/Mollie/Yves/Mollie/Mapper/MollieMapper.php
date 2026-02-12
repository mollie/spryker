<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Mapper;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Mollie\Shared\Mollie\MollieConstants;

class MollieMapper implements MollieMapperInterface
{
    /**
     * @param string $locale
     * @param string $billingCountry
     *
     * @return \Generated\Shared\Transfer\MollieApiRequestTransfer
     */
    public function createMollieApiRequestTransfer(string $locale, string $billingCountry): MollieApiRequestTransfer
    {
        return (new MollieApiRequestTransfer())
            ->setMolliePaymentMethodQueryParameters(
                $this->createMolliePaymentMethodQueryParametersTransfer($locale, $billingCountry),
            );
    }

    /**
     * @param string $locale
     * @param string $billingCountry
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer
     */
    public function createMolliePaymentMethodQueryParametersTransfer(string $locale, string $billingCountry): MolliePaymentMethodQueryParametersTransfer
    {
        return (new MolliePaymentMethodQueryParametersTransfer())
            ->setLocale($locale)
            ->setIncludeIssuers(true)
            ->setBillingCountry($billingCountry)
            ->setSequenceType(MollieConstants::MOLLIE_SEQUENCE_TYPE_ONE_OFF);
    }
}
