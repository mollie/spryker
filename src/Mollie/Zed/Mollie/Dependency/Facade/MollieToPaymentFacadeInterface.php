<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Dependency\Facade;

use Generated\Shared\Transfer\PaymentMethodCollectionTransfer;
use Generated\Shared\Transfer\PaymentMethodCriteriaTransfer;

interface MollieToPaymentFacadeInterface
{
    /**
     * @param \Generated\Shared\Transfer\PaymentMethodCriteriaTransfer $paymentMethodCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodCollectionTransfer
     */
    public function getPaymentMethodCollection(PaymentMethodCriteriaTransfer $paymentMethodCriteriaTransfer): PaymentMethodCollectionTransfer;
}
