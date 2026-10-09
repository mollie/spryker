<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\Order\Expander;

use ArrayObject;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShipmentTransfer;
use Mollie\Zed\Mollie\Business\Exception\ExpressCheckoutOrderException;

class AddressExpander implements ExpressCheckoutQuoteExpanderInterface
{
    /**
     * @var string
     */
    protected const ERROR_MESSAGE_BILLING_ADDRESS_MISSING = 'Express checkout order has no billing address.';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_SHIPPING_ADDRESS_MISSING = 'Express checkout order has no shipping address.';

    /**
     * @uses \Spryker\Shared\Shipment\ShipmentConfig::SHIPMENT_EXPENSE_TYPE
     *
     * @var string
     */
    protected const SHIPMENT_EXPENSE_TYPE = 'SHIPMENT_EXPENSE_TYPE';

    /**
     * Sets the billing address on the quote and one shipment with the shipping address on every item.
     * Addresses from the request win, then addresses already on the quote, then the customer's first stored address.
     * Replacing the shipments drops any previous shipment method, so its shipment expense is removed as well;
     * ShipmentMethodExpander adds a fresh one for the new address.
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer
     *
     * @throws \Mollie\Zed\Mollie\Business\Exception\ExpressCheckoutOrderException
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function expand(
        QuoteTransfer $quoteTransfer,
        MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer,
    ): QuoteTransfer {
        $billingAddressTransfer = $this->resolveAddress(
            $mollieExpressCheckoutOrderRequestTransfer->getBillingAddress(),
            $quoteTransfer->getBillingAddress(),
            $quoteTransfer->getCustomer()?->getBillingAddress(),
        );

        if (!$billingAddressTransfer) {
            throw new ExpressCheckoutOrderException(static::ERROR_MESSAGE_BILLING_ADDRESS_MISSING);
        }

        $shippingAddressTransfer = $this->resolveAddress(
            $mollieExpressCheckoutOrderRequestTransfer->getShippingAddress(),
            $quoteTransfer->getShippingAddress(),
            $quoteTransfer->getCustomer()?->getShippingAddress(),
        );

        if (!$shippingAddressTransfer) {
            throw new ExpressCheckoutOrderException(static::ERROR_MESSAGE_SHIPPING_ADDRESS_MISSING);
        }

        $quoteTransfer->setBillingAddress($billingAddressTransfer);
        $quoteTransfer->setShippingAddress($shippingAddressTransfer);

        $shipmentTransfer = (new ShipmentTransfer())->setShippingAddress($shippingAddressTransfer);
        $quoteTransfer->setShipment($shipmentTransfer);

        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $itemTransfer->setShipment($shipmentTransfer);
        }

        return $this->removeShipmentExpenses($quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function removeShipmentExpenses(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $expenseTransfers = new ArrayObject();
        foreach ($quoteTransfer->getExpenses() as $expenseTransfer) {
            if ($expenseTransfer->getType() !== static::SHIPMENT_EXPENSE_TYPE) {
                $expenseTransfers->append($expenseTransfer);
            }
        }

        return $quoteTransfer->setExpenses($expenseTransfers);
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer|null $requestAddressTransfer
     * @param \Generated\Shared\Transfer\AddressTransfer|null $quoteAddressTransfer
     * @param \ArrayObject<int, \Generated\Shared\Transfer\AddressTransfer>|null $customerAddressTransfers
     *
     * @return \Generated\Shared\Transfer\AddressTransfer|null
     */
    protected function resolveAddress(
        ?AddressTransfer $requestAddressTransfer,
        ?AddressTransfer $quoteAddressTransfer,
        ?ArrayObject $customerAddressTransfers,
    ): ?AddressTransfer {
        if ($this->isFilled($requestAddressTransfer)) {
            return $requestAddressTransfer;
        }

        if ($this->isFilled($quoteAddressTransfer)) {
            return $quoteAddressTransfer;
        }

        if ($customerAddressTransfers && $customerAddressTransfers->count() > 0) {
            return $customerAddressTransfers->offsetGet(0);
        }

        return null;
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer|null $addressTransfer
     *
     * @return bool
     */
    protected function isFilled(?AddressTransfer $addressTransfer): bool
    {
        return $addressTransfer !== null
            && $addressTransfer->getAddress1()
            && $addressTransfer->getCity()
            && $addressTransfer->getIso2Code();
    }
}
