<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\Order\Expander;

use ArrayObject;
use Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Zed\Mollie\Business\Exception\ExpressCheckoutOrderException;
use Mollie\Zed\Mollie\MollieConfig;

class PaymentExpander implements ExpressCheckoutQuoteExpanderInterface
{
    /**
     * @var string
     */
    protected const ERROR_MESSAGE_UNSUPPORTED_EXPRESS_METHOD = 'Express method "%s" has no Spryker payment method.';

    /**
     * @param \Mollie\Zed\Mollie\MollieConfig $config
     */
    public function __construct(protected MollieConfig $config)
    {
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer
     *
     * @throws \Mollie\Zed\Mollie\Business\Exception\ExpressCheckoutOrderException
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function expand(
        QuoteTransfer $quoteTransfer,
        MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer,
    ): QuoteTransfer {
        $expressMethod = $mollieExpressCheckoutOrderRequestTransfer->getExpressMethodOrFail();
        $paymentMethod = $this->config->findExpressCheckoutPaymentMethod($expressMethod);
        $paymentProvider = $this->config->findExpressCheckoutPaymentProvider($expressMethod);

        if (!$paymentMethod || !$paymentProvider) {
            throw new ExpressCheckoutOrderException(sprintf(static::ERROR_MESSAGE_UNSUPPORTED_EXPRESS_METHOD, $expressMethod));
        }

        $paymentTransfer = (new PaymentTransfer())
            ->setPaymentProvider($paymentProvider)
            ->setPaymentMethod($paymentMethod)
            ->setPaymentSelection($paymentMethod);

        $quoteTransfer
            ->setPayment($paymentTransfer)
            ->setPayments(new ArrayObject([$paymentTransfer]))
            ->setMollieExpressCheckoutReference($mollieExpressCheckoutOrderRequestTransfer->getExpressCheckoutReferenceOrFail())
            ->setMolliePaymentId($mollieExpressCheckoutOrderRequestTransfer->getTransactionId());

        return $quoteTransfer;
    }
}
