<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\Handler;

use Generated\Shared\Transfer\MolliePaymentLinkApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkTransfer;

interface MolliePaymentLinkHandlerInterface
{
    /**
     * @param \Generated\Shared\Transfer\MolliePaymentLinkTransfer $molliePaymentLinkTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentLinkApiResponseTransfer
     */
    public function createPaymentLink(MolliePaymentLinkTransfer $molliePaymentLinkTransfer): MolliePaymentLinkApiResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentLinkTransfer $molliePaymentLinkTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentLinkResponseTransfer
     */
    public function updatePaymentLink(MolliePaymentLinkTransfer $molliePaymentLinkTransfer): MolliePaymentLinkResponseTransfer;

//    /**
//     * @return MolliePaymentLinkApiResponseTransfer
//     */
//    public function getPaymentLinks(): MolliePaymentLinkApiResponseTransfer;
}
