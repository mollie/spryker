<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieAvailablePaymentMethodCollectionTransfer;
use Generated\Shared\Transfer\OrderCollectionRequestTransfer;

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
     * - Gets payment by id from Mollie
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\OrderCollectionRequestTransfer
     */
    public function getPaymentById(MollieApiRequestTransfer $mollieApiRequestTransfer): OrderCollectionRequestTransfer;
}
