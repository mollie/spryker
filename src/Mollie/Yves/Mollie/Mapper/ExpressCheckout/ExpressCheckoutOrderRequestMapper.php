<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Mapper\ExpressCheckout;

use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\MollieAddressTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutSessionTransfer;
use Mollie\Service\Mollie\MollieServiceInterface;
use Mollie\Shared\Mollie\MollieConstants;

class ExpressCheckoutOrderRequestMapper implements ExpressCheckoutOrderRequestMapperInterface
{
    /**
     * @param \Mollie\Service\Mollie\MollieServiceInterface $mollieService
     */
    public function __construct(protected MollieServiceInterface $mollieService)
    {
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutSessionTransfer $mollieExpressCheckoutSessionTransfer
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer
     */
    public function mapMollieExpressCheckoutSessionTransferToMollieExpressCheckoutOrderRequestTransfer(
        MollieExpressCheckoutSessionTransfer $mollieExpressCheckoutSessionTransfer,
        MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer,
    ): MollieExpressCheckoutOrderRequestTransfer {
        $mollieBillingAddressTransfer = $mollieExpressCheckoutSessionTransfer->getBillingAddress();
        $mollieShippingAddressTransfer = $mollieExpressCheckoutSessionTransfer->getShippingAddress();

        if ($mollieShippingAddressTransfer === null) {
            $mollieShippingAddressTransfer = $mollieBillingAddressTransfer;
        }

        if ($mollieBillingAddressTransfer !== null) {
            $billingAddressTransfer = $this->mapMollieAddressTransferToAddressTransfer($mollieBillingAddressTransfer, new AddressTransfer());
            $mollieExpressCheckoutOrderRequestTransfer->setBillingAddress($billingAddressTransfer);
        }

        if ($mollieShippingAddressTransfer !== null) {
            $shippingAddressTransfer = $this->mapMollieAddressTransferToAddressTransfer($mollieShippingAddressTransfer, new AddressTransfer());
            $mollieExpressCheckoutOrderRequestTransfer->setShippingAddress($shippingAddressTransfer);
        }

        $shippingFeeReference = $this->findShippingFeeReference($mollieExpressCheckoutSessionTransfer);
        $paidAmount = $this->mollieService->convertMollieAmountToInteger($mollieExpressCheckoutSessionTransfer->getAmount());

        return $mollieExpressCheckoutOrderRequestTransfer
            ->setShipmentMethodKey($shippingFeeReference)
            ->setExpectedGrandTotal($paidAmount);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieAddressTransfer $mollieAddressTransfer
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    protected function mapMollieAddressTransferToAddressTransfer(
        MollieAddressTransfer $mollieAddressTransfer,
        AddressTransfer $addressTransfer,
    ): AddressTransfer {
        return $addressTransfer
            ->setFirstName($mollieAddressTransfer->getGivenName())
            ->setLastName($mollieAddressTransfer->getFamilyName())
            ->setCompany($mollieAddressTransfer->getOrganizationName())
            ->setAddress1($mollieAddressTransfer->getStreetAndNumber())
            ->setAddress2($mollieAddressTransfer->getStreetAdditional())
            ->setZipCode($mollieAddressTransfer->getPostalCode())
            ->setCity($mollieAddressTransfer->getCity())
            ->setRegion($mollieAddressTransfer->getRegion())
            ->setIso2Code($mollieAddressTransfer->getCountry())
            ->setEmail($mollieAddressTransfer->getEmail())
            ->setPhone($mollieAddressTransfer->getPhone());
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutSessionTransfer $mollieExpressCheckoutSessionTransfer
     *
     * @return string|null
     */
    protected function findShippingFeeReference(MollieExpressCheckoutSessionTransfer $mollieExpressCheckoutSessionTransfer): ?string
    {
        foreach ($mollieExpressCheckoutSessionTransfer->getLines() as $mollieExpressCheckoutSessionLineTransfer) {
            if ($mollieExpressCheckoutSessionLineTransfer->getType() !== MollieConstants::PRODUCT_TYPE_SHIPPING_FEE) {
                continue;
            }

            return $mollieExpressCheckoutSessionLineTransfer->getReference();
        }

        return null;
    }
}
