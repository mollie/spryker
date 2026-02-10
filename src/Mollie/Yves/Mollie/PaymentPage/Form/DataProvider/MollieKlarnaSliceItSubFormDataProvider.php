<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\PaymentPage\Form\DataProvider;

use Mollie\Shared\Mollie\MollieConfig;
use Mollie\Yves\Mollie\PaymentPage\Cache\MollieCachedOptionsExpander;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface;

class MollieKlarnaSliceItSubFormDataProvider implements StepEngineFormDataProviderInterface
{
    /**
     * @param \Mollie\Yves\Mollie\PaymentPage\Cache\MollieCachedOptionsExpander $optionsResolver
     */
    public function __construct(
        protected MollieCachedOptionsExpander $optionsResolver,
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
        $paymentMethod = MollieConfig::MOLLIE_PAYMENT_KLARNA_SLICE_IT;

        return $this->optionsResolver->expandOptions($paymentMethod, $quoteTransfer, []);
    }
}
