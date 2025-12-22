<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieAvailablePaymentMethodCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;

interface MollieClientInterface
{
    /**
     *  Specification:
     *  - Gets list of available payment methods from Mollie
     *
     * @api
     *
     * @return \Generated\Shared\Transfer\MollieAvailablePaymentMethodCollectionTransfer
     */
    public function getAvailablePaymentMethods(): MollieAvailablePaymentMethodCollectionTransfer;

    /**
     * Specification:
     * - Creates a payment in Mollie system
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentTransfer
     */
    public function createPayment(MollieApiRequestTransfer $mollieApiRequestTransfer): MolliePaymentTransfer;

    /**
     * Specification:
     * - Gets payment from Mollie system
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentTransfer
     */
    public function getPayment(MollieApiRequestTransfer $mollieApiRequestTransfer): MolliePaymentTransfer;
}
