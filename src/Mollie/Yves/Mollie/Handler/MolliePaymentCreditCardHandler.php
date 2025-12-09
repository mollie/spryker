<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Handler;

use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

class MolliePaymentCreditCardHandler implements MolliePaymentCreditCardHandlerInterface
{
    /**
     * @param \Generated\Shared\Transfer\AbstractTransfer $dataTransfer
     *
     * @return \Generated\Shared\Transfer\AbstractTransfer
     */
    public function addPaymentToQuote(AbstractTransfer $dataTransfer): AbstractTransfer
    {
        return $dataTransfer;
    }
}
