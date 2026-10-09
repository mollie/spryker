<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\Shipping;

use Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsResponseTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShipmentMethodTransfer;
use Generated\Shared\Transfer\ShipmentTransfer;
use Mollie\Service\Mollie\MollieServiceInterface;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToShipmentFacadeInterface;
use Spryker\Shared\Log\LoggerTrait;
use Throwable;

class ExpressCheckoutShippingOptionsProvider implements ExpressCheckoutShippingOptionsProviderInterface
{
    use LoggerTrait;

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_NO_SHIPPING_OPTIONS = 'We do not ship to this address.';

    /**
     * @param \Mollie\Zed\Mollie\Dependency\Facade\MollieToShipmentFacadeInterface $shipmentFacade
     * @param \Mollie\Service\Mollie\MollieServiceInterface $mollieService
     */
    public function __construct(
        protected MollieToShipmentFacadeInterface $shipmentFacade,
        protected MollieServiceInterface $mollieService,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsRequestTransfer $mollieExpressCheckoutShippingOptionsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsResponseTransfer
     */
    public function getShippingOptions(
        MollieExpressCheckoutShippingOptionsRequestTransfer $mollieExpressCheckoutShippingOptionsRequestTransfer,
    ): MollieExpressCheckoutShippingOptionsResponseTransfer {
        $responseTransfer = new MollieExpressCheckoutShippingOptionsResponseTransfer();

        try {
            $quoteTransfer = $this->expandQuoteWithShippingAddress($mollieExpressCheckoutShippingOptionsRequestTransfer);
            $this->addShippingOptions($responseTransfer, $quoteTransfer);
        } catch (Throwable $throwable) {
            $this->getLogger()->error('Express checkout shipping options could not be resolved.', ['exception' => $throwable]);
        }

        if ($responseTransfer->getOptions()->count() === 0) {
            return $responseTransfer
                ->setIsSuccessful(false)
                ->setError(static::ERROR_MESSAGE_NO_SHIPPING_OPTIONS);
        }

        return $responseTransfer->setIsSuccessful(true);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsRequestTransfer $mollieExpressCheckoutShippingOptionsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function expandQuoteWithShippingAddress(
        MollieExpressCheckoutShippingOptionsRequestTransfer $mollieExpressCheckoutShippingOptionsRequestTransfer,
    ): QuoteTransfer {
        $quoteTransfer = $mollieExpressCheckoutShippingOptionsRequestTransfer->getQuoteOrFail();
        $shippingAddressTransfer = $mollieExpressCheckoutShippingOptionsRequestTransfer->getShippingAddressOrFail();

        $shipmentTransfer = (new ShipmentTransfer())->setShippingAddress($shippingAddressTransfer);
        $quoteTransfer->setShippingAddress($shippingAddressTransfer);

        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $itemTransfer->setShipment($shipmentTransfer);
        }

        return $quoteTransfer;
    }

    /**
     * All items share one shipment, so the first shipment group holds the methods for the whole cart.
     *
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsResponseTransfer $responseTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    protected function addShippingOptions(
        MollieExpressCheckoutShippingOptionsResponseTransfer $responseTransfer,
        QuoteTransfer $quoteTransfer,
    ): void {
        $currencyCode = $quoteTransfer->getCurrencyOrFail()->getCodeOrFail();
        $shipmentMethodsCollectionTransfer = $this->shipmentFacade->getAvailableMethodsByShipment($quoteTransfer);
        $usedDescriptions = [];

        foreach ($shipmentMethodsCollectionTransfer->getShipmentMethods() as $shipmentMethodsTransfer) {
            foreach ($shipmentMethodsTransfer->getMethods() as $shipmentMethodTransfer) {
                if ($shipmentMethodTransfer->getStoreCurrencyPrice() === null || !$shipmentMethodTransfer->getShipmentMethodKey()) {
                    continue;
                }

                $description = $this->createUniqueDescription($shipmentMethodTransfer, $usedDescriptions);
                $usedDescriptions[] = $description;

                $responseTransfer->addOption(
                    (new MollieExpressCheckoutShippingOptionTransfer())
                        ->setReference($shipmentMethodTransfer->getShipmentMethodKey())
                        ->setDescription($description)
                        ->setAmount($this->mollieService->convertIntegerToMollieAmount(
                            $shipmentMethodTransfer->getStoreCurrencyPrice(),
                            $currencyCode,
                        )),
                );
            }

            return;
        }
    }

    /**
     * Mollie matches the option chosen in the express sheet against the declared options; two options with the same
     * description (e.g. "Standard" of two carriers) make the payment fail. So the carrier is prefixed and duplicates
     * get the shipment method key appended.
     *
     * @param \Generated\Shared\Transfer\ShipmentMethodTransfer $shipmentMethodTransfer
     * @param array<string> $usedDescriptions
     *
     * @return string
     */
    protected function createUniqueDescription(ShipmentMethodTransfer $shipmentMethodTransfer, array $usedDescriptions): string
    {
        $description = trim(sprintf('%s %s', $shipmentMethodTransfer->getCarrierName(), $shipmentMethodTransfer->getName()));

        if (!in_array($description, $usedDescriptions, true)) {
            return $description;
        }

        return sprintf('%s (%s)', $description, $shipmentMethodTransfer->getShipmentMethodKey());
    }
}
