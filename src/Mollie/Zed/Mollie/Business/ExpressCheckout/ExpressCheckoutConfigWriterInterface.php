<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout;

use Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer;

interface ExpressCheckoutConfigWriterInterface
{
    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer $mollieExpressCheckoutConfigCollectionTransfer
     *
     * @return void
     */
    public function saveExpressCheckoutConfigCollection(
        MollieExpressCheckoutConfigCollectionTransfer $mollieExpressCheckoutConfigCollectionTransfer,
    ): void;
}
