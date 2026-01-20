<?php


declare(strict_types = 1);

namespace Mollie\Client\Mollie\Provider\Payment;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer;

interface PaymentMethodsProviderInterface
{
    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer
     */
    public function getEnabledPaymentMethods(
        MollieApiRequestTransfer $mollieApiRequestTransfer,
    ): MolliePaymentMethodsApiResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer
     */
    public function getAllPaymentMethods(
        MollieApiRequestTransfer $mollieApiRequestTransfer,
    ): MolliePaymentMethodsApiResponseTransfer;
}
