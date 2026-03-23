<?php

namespace Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin;

use Spryker\Yves\Kernel\AbstractPlugin;
use Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;
use Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginInterface;

/**
 * @method \Mollie\Yves\Mollie\MollieFactory getFactory()
 */
class MollieTwintSubFormPlugin extends AbstractPlugin implements SubFormPluginInterface
{
    /**
     * @return SubFormInterface
     */
    public function createSubForm(): SubFormInterface
    {
        return $this->getFactory()->createMollieTwintSubForm();
    }

    /**
     * @return StepEngineFormDataProviderInterface
     */
    public function createSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return $this->getFactory()->createMollieTwintSubFormDataProvider();
    }
}
