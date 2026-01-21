<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Mapper;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Mollie\Shared\Mollie\MollieConstants;

class MollieCommunicationMapper implements MollieCommunicationMapperInterface
{
    /**
     * @param string $locale
     *
     * @return \Generated\Shared\Transfer\MollieApiRequestTransfer
     */
    public function createMollieApiRequestTransfer(string $locale): MollieApiRequestTransfer
    {
        return (new MollieApiRequestTransfer())
            ->setMolliePaymentMethodQueryParameters(
                (new MolliePaymentMethodQueryParametersTransfer())
                    ->setLocale($locale)
                    ->setSequenceType(MollieConstants::MOLLIE_SEQUENCE_TYPE_ONE_OFF),
            );
    }

        /**
         * @param string $locale
         *
         * @return \Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer
         */
    public function createMolliePaymentMethodQueryParametersTransfer(string $locale): MolliePaymentMethodQueryParametersTransfer
    {
        return (new MolliePaymentMethodQueryParametersTransfer())
            ->setLocale($locale)
            ->setSequenceType(MollieConstants::MOLLIE_SEQUENCE_TYPE_ONE_OFF);
    }
}
