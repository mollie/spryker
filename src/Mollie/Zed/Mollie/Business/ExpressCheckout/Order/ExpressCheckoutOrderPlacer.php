<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\Order;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutOrderResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToCheckoutFacadeInterface;
use Spryker\Shared\Log\LoggerTrait;
use Throwable;

class ExpressCheckoutOrderPlacer implements ExpressCheckoutOrderPlacerInterface
{
    use LoggerTrait;

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_PLACE_ORDER_FAILED = 'Express checkout order could not be placed.';

    /**
     * @param \Mollie\Zed\Mollie\Business\ExpressCheckout\Order\ExpressCheckoutQuotePreparerInterface $quotePreparer
     * @param \Mollie\Zed\Mollie\Dependency\Facade\MollieToCheckoutFacadeInterface $checkoutFacade
     */
    public function __construct(
        protected ExpressCheckoutQuotePreparerInterface $quotePreparer,
        protected MollieToCheckoutFacadeInterface $checkoutFacade,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutOrderResponseTransfer
     */
    public function placeOrder(
        MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer,
    ): MollieExpressCheckoutOrderResponseTransfer {
        try {
            $quoteTransfer = $this->prepareQuote($mollieExpressCheckoutOrderRequestTransfer);
            $checkoutResponseTransfer = $this->checkoutFacade->placeOrder($quoteTransfer);
        } catch (Throwable $throwable) {
            $this->getLogger()->error(static::ERROR_MESSAGE_PLACE_ORDER_FAILED, [
                'expressCheckoutReference' => $mollieExpressCheckoutOrderRequestTransfer->getExpressCheckoutReference(),
                'exception' => $throwable,
            ]);

            return $this->createFailedResponse([$throwable->getMessage()]);
        }

        return $this->mapCheckoutResponse($checkoutResponseTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function prepareQuote(MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer): QuoteTransfer
    {
        $quoteTransfer = $this->quotePreparer->prepareQuote($mollieExpressCheckoutOrderRequestTransfer);

        $grandTotal = $quoteTransfer->getTotalsOrFail()->getGrandTotal();
        $quoteTransfer->getPaymentOrFail()->setAmount($grandTotal);

        foreach ($quoteTransfer->getPayments() as $paymentTransfer) {
            $paymentTransfer->setAmount($grandTotal);
        }

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutOrderResponseTransfer
     */
    protected function mapCheckoutResponse(CheckoutResponseTransfer $checkoutResponseTransfer): MollieExpressCheckoutOrderResponseTransfer
    {
        $saveOrderTransfer = $checkoutResponseTransfer->getSaveOrder();

        if (!$checkoutResponseTransfer->getIsSuccess() || !$saveOrderTransfer?->getOrderReference()) {
            $errors = [];
            foreach ($checkoutResponseTransfer->getErrors() as $checkoutErrorTransfer) {
                $errors[] = (string)$checkoutErrorTransfer->getMessage();
            }

            return $this->createFailedResponse($errors ?: [static::ERROR_MESSAGE_PLACE_ORDER_FAILED]);
        }

        return (new MollieExpressCheckoutOrderResponseTransfer())
            ->setIsSuccessful(true)
            ->setOrderReference($saveOrderTransfer->getOrderReference())
            ->setIdSalesOrder($saveOrderTransfer->getIdSalesOrder());
    }

    /**
     * @param array<string> $errors
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutOrderResponseTransfer
     */
    protected function createFailedResponse(array $errors): MollieExpressCheckoutOrderResponseTransfer
    {
        return (new MollieExpressCheckoutOrderResponseTransfer())
            ->setIsSuccessful(false)
            ->setErrors($errors);
    }
}
