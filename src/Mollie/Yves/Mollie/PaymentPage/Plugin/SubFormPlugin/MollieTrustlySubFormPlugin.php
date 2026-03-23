<?php

declare(strict_types = 1);

namespace Mollie\Yves\Mollie\PaymentPage\Plugin\SubFormPlugin;

use Spryker\Yves\Kernel\AbstractPlugin;
use Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface;
use Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginInterface;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;

/**
 * @method \Mollie\Yves\Mollie\MollieFactory getFactory()
 */
class MollieTrustlySubFormPlugin extends AbstractPlugin implements SubFormPluginInterface
{
 /**
  * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
  */
    public function createSubForm(): SubFormInterface
    {
        return $this->getFactory()->createMollieTrustlySubForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createSubFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return $this->getFactory()->createMollieTrustlySubFormDataProvider();
    }
}
