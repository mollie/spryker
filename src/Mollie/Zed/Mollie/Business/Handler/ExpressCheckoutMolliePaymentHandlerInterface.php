<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Handler;

use Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;

interface ExpressCheckoutMolliePaymentHandlerInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return void
     */
    public function createExpressCheckoutMolliePayment(
        QuoteTransfer $quoteTransfer,
        SaveOrderTransfer $saveOrderTransfer,
    ): void;

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer $mollieExpressCheckoutPaymentUpdateRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateResponseTransfer
     */
    public function updateExpressCheckoutMolliePayment(
        MollieExpressCheckoutPaymentUpdateRequestTransfer $mollieExpressCheckoutPaymentUpdateRequestTransfer,
    ): MollieExpressCheckoutPaymentUpdateResponseTransfer;
}
