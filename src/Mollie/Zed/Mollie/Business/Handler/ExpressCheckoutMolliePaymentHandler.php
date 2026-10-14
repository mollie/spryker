<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Handler;

use Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Mollie\Shared\Mollie\MollieConstants;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;

class ExpressCheckoutMolliePaymentHandler implements ExpressCheckoutMolliePaymentHandlerInterface
{
    /**
     * @var string
     */
    protected const METADATA_KEY_ORDER_REFERENCE = 'orderReference';

    /**
     * @param \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface $entityManager
     */
    public function __construct(
        protected MollieEntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return void
     */
    public function createExpressCheckoutMolliePayment(
        QuoteTransfer $quoteTransfer,
        SaveOrderTransfer $saveOrderTransfer,
    ): void {
        $orderReference = $saveOrderTransfer->getOrderReferenceOrFail();

        $paymentTransfer = $quoteTransfer->getPaymentOrFail();
        $mollieExpressPaymentTransfer = $paymentTransfer->getMollieExpressPaymentOrFail();
        $mollieExpressPaymentTransfer->setIdSalesOrder($saveOrderTransfer->getIdSalesOrderOrFail());

        $molliePaymentTransfer = (new MolliePaymentTransfer())
            ->setStatus(MollieConstants::STATUS_OPEN)
            ->setDescription($orderReference)
            ->setSequenceType(MollieConstants::MOLLIE_SEQUENCE_TYPE_ONE_OFF)
            ->setMetadata([static::METADATA_KEY_ORDER_REFERENCE => $orderReference]);

        $this->entityManager->createExpressCheckoutMolliePayment($mollieExpressPaymentTransfer, $molliePaymentTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer $mollieExpressCheckoutPaymentUpdateRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateResponseTransfer
     */
    public function updateExpressCheckoutMolliePayment(
        MollieExpressCheckoutPaymentUpdateRequestTransfer $mollieExpressCheckoutPaymentUpdateRequestTransfer,
    ): MollieExpressCheckoutPaymentUpdateResponseTransfer {
        $isUpdated = $this->entityManager->updateExpressCheckoutMolliePayment($mollieExpressCheckoutPaymentUpdateRequestTransfer);

        $mollieExpressCheckoutPaymentUpdateResponseTransfer = new MollieExpressCheckoutPaymentUpdateResponseTransfer();
        $mollieExpressCheckoutPaymentUpdateResponseTransfer->setIsExpressPaymentFound($isUpdated);

        return $mollieExpressCheckoutPaymentUpdateResponseTransfer;
    }
}
