<?php

namespace Mollie\Zed\Mollie\Business\Handler;

use Generated\Shared\Transfer\MolliePaymentLinkApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkTransfer;

interface MolliePaymentLinkHandlerInterface
{
    /**
     * @param MolliePaymentLinkTransfer $molliePaymentLinkTransfer
     * @return MolliePaymentLinkTransfer
     */
    public function createPaymentLink(MolliePaymentLinkTransfer $molliePaymentLinkTransfer): MolliePaymentLinkApiResponseTransfer

//    /**
//     * @return MolliePaymentLinkApiResponseTransfer
//     */
//    public function getPaymentLinks(): MolliePaymentLinkApiResponseTransfer;
}