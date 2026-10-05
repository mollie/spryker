<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\Order\Expander;

use Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShipmentMethodTransfer;
use Mollie\Zed\Mollie\Business\Exception\ExpressCheckoutOrderException;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToShipmentFacadeInterface;

class ShipmentMethodExpander implements ExpressCheckoutQuoteExpanderInterface
{
    /**
     * @var string
     */
    protected const ERROR_MESSAGE_SHIPMENT_METHOD_NOT_AVAILABLE = 'The chosen shipping method "%s" is not available for this order.';

    /**
     * @param \Mollie\Zed\Mollie\Dependency\Facade\MollieToShipmentFacadeInterface $shipmentFacade
     */
    public function __construct(protected MollieToShipmentFacadeInterface $shipmentFacade)
    {
    }

    /**
     * Assigns the shipment method the shopper chose in the express sheet (request `shipmentMethodKey`) to the item
     * shipments and adds its expense. The method is taken from the methods available for the quote, so it is valid
     * for the shipping address and carries the store/currency price.
     * Requires the item shipments to have a shipping address (see AddressExpander).
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
        $shipmentMethodKey = $mollieExpressCheckoutOrderRequestTransfer->getShipmentMethodKeyOrFail();
        $shipmentMethodTransfer = $this->findAvailableShipmentMethodByKey($quoteTransfer, $shipmentMethodKey);

        if (!$shipmentMethodTransfer) {
            throw new ExpressCheckoutOrderException(sprintf(static::ERROR_MESSAGE_SHIPMENT_METHOD_NOT_AVAILABLE, $shipmentMethodKey));
        }

        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $itemTransfer->getShipmentOrFail()
                ->setMethod($shipmentMethodTransfer)
                ->setShipmentSelection((string)$shipmentMethodTransfer->getIdShipmentMethod());
        }

        // Recalculation happens once for the whole quote in ExpressCheckoutOrderPlacer.
        $quoteTransfer->setSkipRecalculation(true);

        return $this->shipmentFacade->expandQuoteWithShipmentGroups($quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param string $shipmentMethodKey
     *
     * @return \Generated\Shared\Transfer\ShipmentMethodTransfer|null
     */
    protected function findAvailableShipmentMethodByKey(QuoteTransfer $quoteTransfer, string $shipmentMethodKey): ?ShipmentMethodTransfer
    {
        $shipmentMethodsCollectionTransfer = $this->shipmentFacade->getAvailableMethodsByShipment($quoteTransfer);

        foreach ($shipmentMethodsCollectionTransfer->getShipmentMethods() as $shipmentMethodsTransfer) {
            foreach ($shipmentMethodsTransfer->getMethods() as $shipmentMethodTransfer) {
                if ($shipmentMethodTransfer->getShipmentMethodKey() === $shipmentMethodKey) {
                    return $shipmentMethodTransfer;
                }
            }
        }

        return null;
    }
}
