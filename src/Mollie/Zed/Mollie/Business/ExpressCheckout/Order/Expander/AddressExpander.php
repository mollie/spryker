<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\Order\Expander;

use Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShipmentTransfer;

class AddressExpander implements ExpressCheckoutQuoteExpanderInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function expand(
        QuoteTransfer $quoteTransfer,
        MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer,
    ): QuoteTransfer {
        $billingAddressTransfer = $mollieExpressCheckoutOrderRequestTransfer->getBillingAddressOrFail();
        $shippingAddressTransfer = $mollieExpressCheckoutOrderRequestTransfer->getShippingAddressOrFail();

        $shipmentTransfer = (new ShipmentTransfer())->setShippingAddress($shippingAddressTransfer);

        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $itemTransfer->setShipment($shipmentTransfer);
        }

        return $quoteTransfer
            ->setBillingAddress($billingAddressTransfer)
            ->setShippingAddress($shippingAddressTransfer)
            ->setShipment($shipmentTransfer);
    }
}
