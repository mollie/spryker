<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie;

use Mollie\Yves\Mollie\Handler\MolliePaymentCreditCardHandler;
use Mollie\Yves\Mollie\Handler\MolliePaymentCreditCardHandlerInterface;
use Mollie\Yves\Mollie\PaymentPage\Form\DataProvider\MollieCreditCardSubFormDataProvider;
use Mollie\Yves\Mollie\PaymentPage\Form\MollieCreditCardSubForm;
use Spryker\Yves\Kernel\AbstractFactory;
use Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;

class MollieFactory extends AbstractFactory
{
    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createMollieCreditCardSubForm(): SubFormInterface
    {
        return new MollieCreditCardSubForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createMollieCreditCardSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new MollieCreditCardSubFormDataProvider();
    }

    /**
     * @return \Mollie\Yves\Mollie\Handler\MolliePaymentCreditCardHandlerInterface
     */
    public function createMolliePaymentHandler(): MolliePaymentCreditCardHandlerInterface
    {
        return new MolliePaymentCreditCardHandler();
    }
}
