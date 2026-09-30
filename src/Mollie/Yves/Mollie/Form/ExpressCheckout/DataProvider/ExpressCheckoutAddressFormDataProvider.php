<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Form\ExpressCheckout\DataProvider;

use ArrayObject;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Yves\Mollie\Dependency\Client\MollieToStoreClientInterface;
use Mollie\Yves\Mollie\Form\ExpressCheckout\ExpressCheckoutAddressForm;

class ExpressCheckoutAddressFormDataProvider
{
    /**
     * @var string
     */
    protected const COUNTRY_GLOSSARY_KEY_PREFIX = 'countries.iso.';

    /**
     * @param \Mollie\Yves\Mollie\Dependency\Client\MollieToStoreClientInterface $storeClient
     */
    public function __construct(protected MollieToStoreClientInterface $storeClient)
    {
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array<string, mixed>
     */
    public function getData(QuoteTransfer $quoteTransfer): array
    {
        $billingAddressTransfer = $this->resolveAddress(
            $quoteTransfer->getBillingAddress(),
            $quoteTransfer->getCustomer()?->getBillingAddress(),
        );
        $shippingAddressTransfer = $this->resolveAddress(
            $quoteTransfer->getShippingAddress(),
            $quoteTransfer->getCustomer()?->getShippingAddress(),
        );

        return [
            ExpressCheckoutAddressForm::FIELD_BILLING_ADDRESS => $billingAddressTransfer,
            ExpressCheckoutAddressForm::FIELD_SHIPPING_ADDRESS => $shippingAddressTransfer,
            ExpressCheckoutAddressForm::FIELD_SHIPPING_SAME_AS_BILLING => $this->isSameAddress($billingAddressTransfer, $shippingAddressTransfer),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        $countryChoices = [];
        foreach ($this->storeClient->getCurrentStore()->getCountries() as $iso2Code) {
            $countryChoices[static::COUNTRY_GLOSSARY_KEY_PREFIX . $iso2Code] = $iso2Code;
        }

        return [
            ExpressCheckoutAddressForm::OPTION_COUNTRY_CHOICES => $countryChoices,
        ];
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer|null $quoteAddressTransfer
     * @param \ArrayObject<int, \Generated\Shared\Transfer\AddressTransfer>|null $customerAddressTransfers
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    protected function resolveAddress(?AddressTransfer $quoteAddressTransfer, ?ArrayObject $customerAddressTransfers): AddressTransfer
    {
        if ($quoteAddressTransfer && $quoteAddressTransfer->getAddress1()) {
            return clone $quoteAddressTransfer;
        }

        if ($customerAddressTransfers && $customerAddressTransfers->count() > 0) {
            return clone $customerAddressTransfers->offsetGet(0);
        }

        return new AddressTransfer();
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $billingAddressTransfer
     * @param \Generated\Shared\Transfer\AddressTransfer $shippingAddressTransfer
     *
     * @return bool
     */
    protected function isSameAddress(AddressTransfer $billingAddressTransfer, AddressTransfer $shippingAddressTransfer): bool
    {
        return !$shippingAddressTransfer->getAddress1()
            || ($billingAddressTransfer->getAddress1() === $shippingAddressTransfer->getAddress1()
                && $billingAddressTransfer->getZipCode() === $shippingAddressTransfer->getZipCode()
                && $billingAddressTransfer->getLastName() === $shippingAddressTransfer->getLastName());
    }
}
