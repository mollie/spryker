<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Handler;

use Exception;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Shared\Mollie\MollieConfig as MollieMollieConfig;
use Mollie\Zed\Mollie\Business\Exception\MolliePaymentException;
use Mollie\Zed\Mollie\Dependency\MollieToStorageClientInterface;
use Mollie\Zed\Mollie\MollieConfig;

class MolliePaymentHandler implements MolliePaymentHandlerInterface
{
    /**
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     * @param \Mollie\Zed\Mollie\Dependency\MollieToStorageClientInterface $storageClient
     */
    public function __construct(
        protected MollieClientInterface $mollieClient,
        protected MollieToStorageClientInterface $storageClient,
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
            $this->savePaymentIdToStorage($molliePaymentTransfer->getId(), $checkoutResponseTransfer->getSaveOrderOrFail()->getOrderReference());
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
     * @param string $paymentId
     * @param string $orderReference
     *
     * @return void
     */
    protected function savePaymentIdToStorage(string $paymentId, string $orderReference): void
    {
        $key = sprintf('%s:%s', MollieMollieConfig::MOLLIE_STORAGE_KEY_PREFIX, $orderReference);
        $this->storageClient->set($key, $paymentId, MollieMollieConfig::MOLLIE_STORAGE_TTL);
    }
}
