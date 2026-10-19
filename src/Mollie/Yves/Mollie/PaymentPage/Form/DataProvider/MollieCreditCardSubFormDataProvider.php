<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\PaymentPage\Form\DataProvider;

use Generated\Shared\Transfer\MollieSingleClickPaymentDataRequestTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Shared\Mollie\MollieConfig as SharedConfig;
use Mollie\Yves\Mollie\MollieConfig;
use Mollie\Yves\Mollie\PaymentPage\Cache\MollieCachedOptionsExpander;
use Mollie\Yves\Mollie\PaymentPage\Form\MollieCreditCardSubForm;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface;

class MollieCreditCardSubFormDataProvider implements StepEngineFormDataProviderInterface
{
    /**
     * @param \Mollie\Yves\Mollie\PaymentPage\Cache\MollieCachedOptionsExpander $optionsExpander
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     * @param \Mollie\Yves\Mollie\MollieConfig $config
     */
    public function __construct(
        protected MollieCachedOptionsExpander $optionsExpander,
        protected MollieClientInterface $mollieClient,
        protected MollieConfig $config,
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
        $paymentMethod = SharedConfig::MOLLIE_PAYMENT_CREDIT_CARD;

        $options = $this->optionsExpander->expandOptions($paymentMethod, $quoteTransfer, []);

        return $this->expandWithSingleClickOptions($quoteTransfer, $options);
    }

    /**
     * Specification:
     * - Single-click needs Mollie Components (card token) and a logged-in customer.
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    protected function expandWithSingleClickOptions(QuoteTransfer $quoteTransfer, array $options): array
    {
        if (!$this->config->isMollieCreditCardComponentEnabled()) {
            return $options;
        }

        $idCustomer = $quoteTransfer->getCustomer()->getIdCustomer();
        if (!$idCustomer) {
            return $options;
        }

        $mollieSingleClickPaymentDataTransfer = $this->mollieClient->getSingleClickPaymentData(
            (new MollieSingleClickPaymentDataRequestTransfer())->setIdCustomer($idCustomer),
        );

        $options[MollieCreditCardSubForm::OPTION_SINGLE_CLICK_PAYMENT_DATA] = $mollieSingleClickPaymentDataTransfer;

        return $options;
    }
}
