<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\PaymentPage\Form\DataProvider;

use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Service\Mollie\MollieServiceInterface;
use Mollie\Shared\Mollie\MollieConfig;
use Mollie\Yves\Mollie\PaymentPage\Cache\MollieCachedOptionsExpander;
use Mollie\Yves\Mollie\PaymentPage\Form\MollieApplePayDirectSubForm;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface;

class MollieApplePayDirectSubFormDataProvider implements StepEngineFormDataProviderInterface
{
    /**
     * @param \Mollie\Yves\Mollie\PaymentPage\Cache\MollieCachedOptionsExpander $optionsExpander
     * @param \Mollie\Service\Mollie\MollieServiceInterface $mollieService
     */
    public function __construct(
        protected MollieCachedOptionsExpander $optionsExpander,
        protected MollieServiceInterface $mollieService,
    ) {
    }

    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $dataTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    public function getData(AbstractTransfer $dataTransfer): AbstractTransfer
    {
        return $dataTransfer;
    }

    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $dataTransfer
     *
     * @return array<mixed>
     */
    public function getOptions(AbstractTransfer $dataTransfer): array
    {
        /** @var \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer */
        $quoteTransfer = $dataTransfer;
        $paymentMethod = MollieConfig::MOLLIE_PAYMENT_APPLE_PAY_DIRECT;
        $applePayOptions = $this->createApplePayOptions($quoteTransfer);

        return $this->optionsExpander->expandOptions($paymentMethod, $quoteTransfer, $applePayOptions);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array<string, string>
     */
    protected function createApplePayOptions(QuoteTransfer $quoteTransfer): array
    {
        $currencyTransfer = $quoteTransfer->getCurrencyOrFail();
        $currencyCode = $currencyTransfer->getCodeOrFail();

        $totalsTransfer = $quoteTransfer->getTotalsOrFail();
        $grandTotal = $totalsTransfer->getGrandTotalOrFail();
        $mollieAmountTransfer = $this->mollieService->convertIntegerToMollieAmount($grandTotal, $currencyCode);
        $amount = $mollieAmountTransfer->getValueOrFail();

        return [
            MollieApplePayDirectSubForm::OPTION_APPLE_PAY_AMOUNT => $amount,
            MollieApplePayDirectSubForm::OPTION_APPLE_PAY_CURRENCY_CODE => $currencyCode,
        ];
    }
}
