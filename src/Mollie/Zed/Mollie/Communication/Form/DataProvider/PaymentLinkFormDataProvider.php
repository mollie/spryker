<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Form\DataProvider;

use Generated\Shared\Transfer\CurrencyCriteriaTransfer;
use Generated\Shared\Transfer\PaymentMethodCriteriaTransfer;
use Mollie\Shared\Mollie\MollieConstants;
use Mollie\Zed\Mollie\Communication\Form\CreatePaymentLinkForm;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToCurrencyFacadeInterface;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToPaymentFacadeInterface;
use Mollie\Zed\Mollie\MollieConfig;

class PaymentLinkFormDataProvider
{
    /**
     * @param \Mollie\Zed\Mollie\Dependency\Facade\MollieToCurrencyFacadeInterface $currencyFacade
     * @param \Mollie\Zed\Mollie\Dependency\Facade\MollieToPaymentFacadeInterface $paymentFacade
     */
    public function __construct(
        protected MollieToCurrencyFacadeInterface $currencyFacade,
        protected MollieToPaymentFacadeInterface $paymentFacade,
    ) {
    }

    /**
     * @return array<mixed>
     */
    public function getData(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return [
            CreatePaymentLinkForm::OPTION_PAYMENT_TYPES => $this->getPaymentTypes(),
            CreatePaymentLinkForm::OPTION_CURRENCY_CODES => $this->getCurrencyCodes(),
            CreatePaymentLinkForm::OPTION_AVAILABLE_PAYMENT_METHODS => $this->getAvailablePaymentMethods(),
        ];
    }

    /**
     * @return array<string>
     */
    protected function getPaymentTypes(): array
    {
        return MollieConstants::PAYMENT_LINK_TYPES;
    }

    /**
     * @return array<int, string>
     */
    protected function getCurrencyCodes(): array
    {
        $currencyCriteriaTransfer = new CurrencyCriteriaTransfer();
        $currencyCollectionTransfer = $this->currencyFacade->getCurrencyCollection($currencyCriteriaTransfer);
        $currencyCodes = [];
        foreach ($currencyCollectionTransfer->getCurrencies() as $currency) {
            $currencyCodes[$currency->getCode()] = $currency->getCode();
        }

        return $currencyCodes;
    }

    /**
     * @return array<string, string>
     */
    protected function getAvailablePaymentMethods(): array
    {
        $paymentMethodCriteriaTransfer = new PaymentMethodCriteriaTransfer();
        $paymentMethodCollectionTransfer = $this->paymentFacade->getPaymentMethodCollection($paymentMethodCriteriaTransfer);
        $paymentMethods = [];
        foreach ($paymentMethodCollectionTransfer->getPaymentMethods() as $paymentMethod) {
            if (!str_starts_with($paymentMethod->getPaymentProvider()?->getPaymentProviderKey(), MollieConfig::PAYMENT_PROVIDER_PREFIX)) {
                continue;
            }
            $paymentMethods[$paymentMethod->getPaymentMethodKey()] = $paymentMethod->getName();
        }

        return $paymentMethods;
    }
}
