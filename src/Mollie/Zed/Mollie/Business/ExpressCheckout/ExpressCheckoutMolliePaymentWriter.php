<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;

class ExpressCheckoutMolliePaymentWriter implements ExpressCheckoutMolliePaymentWriterInterface
{
    /**
     * @param \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface $entityManager
     */
    public function __construct(
        protected MollieEntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return void
     */
    public function createExpressCheckoutMolliePayment(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer,
    ): void {
        $idSalesOrder = $checkoutResponseTransfer->getSaveOrderOrFail()->getIdSalesOrderOrFail();
        $expressCheckoutUuid = $quoteTransfer->getMollieExpressCheckoutUuidOrFail();

        $this->entityManager->createExpressCheckoutMolliePayment($idSalesOrder, $expressCheckoutUuid);
    }
}
