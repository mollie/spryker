<?php


declare(strict_types = 1);

namespace Mollie\Yves\Mollie\Handler;

use Mollie\Yves\Mollie\MollieConfig;
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
        $paymentTransfer = $dataTransfer->getPayment();

        $paymentTransfer
            ->setPaymentProvider(MollieConfig::PROVIDER_NAME)
            ->setPaymentMethod(MollieConfig::MOLLIE_PAYMENT_CREDIT_CARD);

        return $dataTransfer;
    }
}
