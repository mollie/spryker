<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Form\DataProvider;

use Mollie\Zed\Mollie\Dependency\Facade\MollieToStoreFacadeInterface;

class PaymentMethodsFilterFormDataProvider
{
    public const string OPTION_CURRENCIES = 'currencies';

    /**
     * @param \Mollie\Zed\Mollie\Dependency\Facade\MollieToStoreFacadeInterface $storeFacade
     */
    public function __construct(
        private MollieToStoreFacadeInterface $storeFacade,
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
            static::OPTION_CURRENCIES => $this->getCurrencyOptionsForAllStores(),
        ];
    }

    /**
     * @return array<string>
     */
    protected function getCurrencyOptionsForAllStores(): array
    {
        $currencies = [];
        $storeTransfers = $this->storeFacade->getAllStores();
        foreach ($storeTransfers as $storeTransfer) {
            foreach ($storeTransfer->getAvailableCurrencyIsoCodes() as $currencyIsoCode) {
                $currencies[$currencyIsoCode] = $currencyIsoCode;
            }
        }

        return $currencies;
    }
}
