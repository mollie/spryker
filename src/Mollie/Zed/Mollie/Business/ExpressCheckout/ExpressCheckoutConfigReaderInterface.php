<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout;

use Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer;

interface ExpressCheckoutConfigReaderInterface
{
    /**
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer
     */
    public function getExpressCheckoutConfigCollection(): MollieExpressCheckoutConfigCollectionTransfer;
}
