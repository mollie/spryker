<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Mapper\ExpressCheckout;

use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutSessionTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

class ExpressCheckoutOrderRequestMapper implements ExpressCheckoutOrderRequestMapperInterface
{
    /**
     * @var array<string, string>
     */
    protected const ADDRESS_FIELD_MAPPING = [
        'givenName' => 'firstName',
        'familyName' => 'lastName',
        'organizationName' => 'company',
        'streetAndNumber' => 'address1',
        'streetAdditional' => 'address2',
        'postalCode' => 'zipCode',
        'city' => 'city',
        'region' => 'region',
        'country' => 'iso2Code',
        'email' => 'email',
        'phone' => 'phone',
    ];

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutSessionTransfer $mollieExpressCheckoutSessionTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param string $expressCheckoutUuid
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer
     */
    public function mapSessionToOrderRequest(
        MollieExpressCheckoutSessionTransfer $mollieExpressCheckoutSessionTransfer,
        QuoteTransfer $quoteTransfer,
        string $expressCheckoutUuid,
    ): MollieExpressCheckoutOrderRequestTransfer {
        $billingAddress = $mollieExpressCheckoutSessionTransfer->getBillingAddress();
        $shippingAddress = $mollieExpressCheckoutSessionTransfer->getShippingAddress() ?: $billingAddress;

        return (new MollieExpressCheckoutOrderRequestTransfer())
            ->setQuote($quoteTransfer)
            ->setExpressCheckoutUuid($expressCheckoutUuid)
            ->setBillingAddress($this->mapAddress($billingAddress))
            ->setShippingAddress($this->mapAddress($shippingAddress))
            ->setShipmentMethodKey($mollieExpressCheckoutSessionTransfer->getShippingFeeReference())
            ->setExpectedGrandTotal($this->findPaidAmount($mollieExpressCheckoutSessionTransfer));
    }

    /**
     * @param array<string, mixed> $mollieAddress
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    protected function mapAddress(array $mollieAddress): AddressTransfer
    {
        $addressData = [];
        foreach (static::ADDRESS_FIELD_MAPPING as $mollieField => $sprykerField) {
            if (isset($mollieAddress[$mollieField]) && $mollieAddress[$mollieField] !== '') {
                $addressData[$sprykerField] = $mollieAddress[$mollieField];
            }
        }

        return (new AddressTransfer())->fromArray($addressData, true);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutSessionTransfer $mollieExpressCheckoutSessionTransfer
     *
     * @return int|null
     */
    protected function findPaidAmount(MollieExpressCheckoutSessionTransfer $mollieExpressCheckoutSessionTransfer): ?int
    {
        $value = $mollieExpressCheckoutSessionTransfer->getAmount()?->getValue();

        return $value === null ? null : (int)round((float)$value * 100);
    }
}
