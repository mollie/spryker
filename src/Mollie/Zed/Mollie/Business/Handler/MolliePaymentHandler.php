<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Handler;

use Exception;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Client\Mollie\MollieConfig;
use Mollie\Zed\Mollie\Business\Exception\MolliePaymentException;

class MolliePaymentHandler implements MolliePaymentHandlerInterface
{
    /**
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     */
    public function __construct(
        protected MollieClientInterface $mollieClient,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @throws \Mollie\Zed\Mollie\Business\Exception\MolliePaymentException
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function createPayment(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): CheckoutResponseTransfer
    {
        try {
            $mollieApiRequestTransfer = (new MollieApiRequestTransfer())
                ->setCheckoutResponse($checkoutResponseTransfer)
                ->setQuote($quoteTransfer);

            $molliePaymentTransfer = $this->mollieClient->createPayment($mollieApiRequestTransfer);

            $quoteTransfer = $this->addPaymentDataToQuote($quoteTransfer, $molliePaymentTransfer);
        } catch (Exception $e) {
            throw new MolliePaymentException(
                'Mollie payment could not be created. ' . $e->getMessage(),
                0,
                $e,
            );
        }

        return $checkoutResponseTransfer
            ->setIsSuccess(true)
            ->setIsExternalRedirect(true)
            ->setRedirectUrl($molliePaymentTransfer->getLinks()[MollieConfig::RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS_CHECKOUT][MollieConfig::RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS_HREF] ?? null);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function addPaymentDataToQuote(QuoteTransfer &$quoteTransfer, MolliePaymentTransfer $molliePaymentTransfer): QuoteTransfer
    {
        $paymentId = $molliePaymentTransfer->getId();
        $quoteTransfer->setMolliePaymentId($paymentId);

        return $quoteTransfer;
    }
}
