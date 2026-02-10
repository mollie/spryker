<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Plugin\PaymentHandler;

use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Yves\Kernel\AbstractPlugin;
use Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Mollie\Yves\Mollie\MollieFactory getFactory()
 */
class MolliePayByBankPaymentHandlerPlugin extends AbstractPlugin implements StepHandlerPluginInterface
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $dataTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    public function addToDataClass(Request $request, AbstractTransfer $dataTransfer): AbstractTransfer
    {
        return $this->getFactory()->createMolliePayByBankPaymentHandler()->addPaymentToQuote($dataTransfer);
    }
}
