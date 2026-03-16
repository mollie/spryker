<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Dependency\Facade;

use Generated\Shared\Transfer\PaymentMethodCollectionTransfer;
use Generated\Shared\Transfer\PaymentMethodCriteriaTransfer;
use Spryker\Zed\Payment\Business\PaymentFacadeInterface;

class MollieToPaymentFacadeBridge implements MollieToPaymentFacadeInterface
{
    protected PaymentFacadeInterface $paymentFacade;

    /**
     * @param \Spryker\Zed\Payment\Business\PaymentFacadeInterface $paymentFacade
     */
    public function __construct($paymentFacade)
    {
        $this->paymentFacade = $paymentFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodCriteriaTransfer $paymentMethodCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodCollectionTransfer
     */
    public function getPaymentMethodCollection(PaymentMethodCriteriaTransfer $paymentMethodCriteriaTransfer): PaymentMethodCollectionTransfer
    {
        return $this->paymentFacade->getPaymentMethodCollection($paymentMethodCriteriaTransfer);
    }
}
