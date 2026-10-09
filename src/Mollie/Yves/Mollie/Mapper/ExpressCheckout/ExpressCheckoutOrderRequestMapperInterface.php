<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Mapper\ExpressCheckout;

use Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutSessionTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface ExpressCheckoutOrderRequestMapperInterface
{
    /**
     * Specification:
     * - Builds the order request from the completed Mollie session: billing and shipping address (Mollie format → Spryker),
     *   the shipping method the shopper chose in the express sheet (`shipping_fee` line reference) and the paid amount.
     *
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutSessionTransfer $mollieExpressCheckoutSessionTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param string $expressCheckoutUuid
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer
     */
    public function mapSessionToOrderRequest(
        MollieExpressCheckoutSessionTransfer $mollieExpressCheckoutSessionTransfer,
        QuoteTransfer $quoteTransfer,
        string $expressCheckoutUuid,
    ): MollieExpressCheckoutOrderRequestTransfer;
}
