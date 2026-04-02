<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Form\DataProvider;

use Generated\Shared\Transfer\CurrencyCriteriaTransfer;
use Mollie\Zed\Mollie\Communication\Form\CreatePaymentLinkForm;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToCurrencyFacadeInterface;
use Mollie\Zed\Mollie\MollieConfig;

class PaymentLinkFormDataProvider
{
    /**
     * @param \Mollie\Zed\Mollie\Dependency\Facade\MollieToCurrencyFacadeInterface $currencyFacade
     * @param \Mollie\Zed\Mollie\MollieConfig $config
     */
    public function __construct(
        protected MollieToCurrencyFacadeInterface $currencyFacade,
        protected MollieConfig $config,
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
            CreatePaymentLinkForm::OPTION_CURRENCY_CODES => $this->getCurrencyCodes(),
            CreatePaymentLinkForm::OPTION_AVAILABLE_PAYMENT_METHODS => $this->getAvailablePaymentMethods(),
        ];
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
        return $this->config->getMollieOmsToPaymentMethodMapping();
    }
}
