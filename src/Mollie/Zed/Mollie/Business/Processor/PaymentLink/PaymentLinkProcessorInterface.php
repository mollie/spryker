<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Processor\PaymentLink;

use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Generated\Shared\Transfer\OrderTransfer;

interface PaymentLinkProcessorInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentLinkTransfer
     */
    public function processOrderItemPaymentLink(OrderTransfer $orderTransfer): MolliePaymentLinkTransfer;
}
