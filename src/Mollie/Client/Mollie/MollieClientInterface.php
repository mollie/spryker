<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie;

use Generated\Shared\Transfer\MollieAvailablePaymentMethodCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodTransfer;

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
      * - Gets IDEAL payment method from mollie
      *
      * @api
      *
      * @return \Generated\Shared\Transfer\MolliePaymentMethodTransfer
      */
    public function getIdealPaymentMethod(): MolliePaymentMethodTransfer;
}
