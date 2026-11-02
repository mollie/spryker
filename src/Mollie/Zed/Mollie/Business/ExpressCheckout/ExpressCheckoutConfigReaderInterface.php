<?php


declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout;

use Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutConfigCriteriaTransfer;

interface ExpressCheckoutConfigReaderInterface
{
    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutConfigCriteriaTransfer $criteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer
     */
    public function getExpressCheckoutConfigCollection(
        MollieExpressCheckoutConfigCriteriaTransfer $criteriaTransfer,
    ): MollieExpressCheckoutConfigCollectionTransfer;
}
