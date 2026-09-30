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
    protected const ERROR_MESSAGE_NO_SHIPMENT_METHOD = 'No shipment method is available for the express checkout order.';

    /**
     * @param \Mollie\Zed\Mollie\Dependency\Facade\MollieToShipmentFacadeInterface $shipmentFacade
     */
    public function __construct(protected MollieToShipmentFacadeInterface $shipmentFacade)
    {
    }

    /**
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
        $shipmentMethodTransfer = $this->findFirstAvailableShipmentMethod($quoteTransfer);

        if (!$shipmentMethodTransfer) {
            throw new ExpressCheckoutOrderException(static::ERROR_MESSAGE_NO_SHIPMENT_METHOD);
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
     *
     * @return \Generated\Shared\Transfer\ShipmentMethodTransfer|null
     */
    protected function findFirstAvailableShipmentMethod(QuoteTransfer $quoteTransfer): ?ShipmentMethodTransfer
    {
        $shipmentMethodsCollectionTransfer = $this->shipmentFacade->getAvailableMethodsByShipment($quoteTransfer);

        foreach ($shipmentMethodsCollectionTransfer->getShipmentMethods() as $shipmentMethodsTransfer) {
            foreach ($shipmentMethodsTransfer->getMethods() as $shipmentMethodTransfer) {
                return $shipmentMethodTransfer;
            }
        }

        return null;
    }
}
