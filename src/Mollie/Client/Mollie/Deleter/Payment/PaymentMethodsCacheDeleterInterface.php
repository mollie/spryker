<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie\Deleter\Payment;

use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;

interface PaymentMethodsCacheDeleterInterface
{
     /**
      * @param \Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer $parameters
      *
      * @return void
      */
    public function deleteEnabledPaymentMethodsCache(MolliePaymentMethodQueryParametersTransfer $parameters): void;

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer $parameters
     *
     * @return void
     */
    public function deleteAllPaymentMethodsCache(MolliePaymentMethodQueryParametersTransfer $parameters): void;
}
