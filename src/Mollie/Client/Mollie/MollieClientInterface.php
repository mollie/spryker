<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieAvailablePaymentMethodsApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentApiResponseTransfer;

interface MollieClientInterface
{
    /**
     *  Specification:
     *  - Gets list of available payment methods from Mollie
     *
     * @api
     *
     * @return \Generated\Shared\Transfer\MollieAvailablePaymentMethodsApiResponseTransfer
     */
    public function getAvailablePaymentMethods(): MollieAvailablePaymentMethodsApiResponseTransfer;

    /**
     * Specification:
     * - Gets payment by transaction id from Mollie
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentApiResponseTransfer
     */
    public function getPaymentByTransactionId(MollieApiRequestTransfer $mollieApiRequestTransfer): MolliePaymentApiResponseTransfer;
}
