<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Form\DataProvider;

use Mollie\Zed\Mollie\Business\MollieFacadeInterface;
use Mollie\Zed\Mollie\Communication\Form\SingleClickConfigForm;

class SingleClickConfigFormDataProvider
{
    /**
     * @param \Mollie\Zed\Mollie\Business\MollieFacadeInterface $mollieFacade
     */
    public function __construct(
        protected MollieFacadeInterface $mollieFacade,
    ) {
    }

    /**
     * @return array<string, bool>
     */
    public function getData(): array
    {
        return [
            SingleClickConfigForm::FIELD_IS_ENABLED => $this->mollieFacade->isSingleClickPaymentEnabled(),
        ];
    }
}
