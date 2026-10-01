<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Handler;

use ArrayObject;
use Generated\Shared\Transfer\CheckoutErrorTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentApiResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Shared\Mollie\MollieConstants;
use Mollie\Zed\Mollie\Business\Writer\MolliePaymentWriterInterface;
use Mollie\Zed\Mollie\Dependency\MollieToStorageClientInterface;
use Mollie\Zed\Mollie\MollieConfig;
use Spryker\Shared\Log\LoggerTrait;

class MolliePaymentHandler implements MolliePaymentHandlerInterface
{
    use LoggerTrait;

    /**
     * @var string
     */
    protected const DEFAULT_ERROR_MESSAGE = 'checkout.step.error.text';

    /**
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     * @param \Mollie\Zed\Mollie\Dependency\MollieToStorageClientInterface $storageClient
     * @param \Mollie\Zed\Mollie\Business\Writer\MolliePaymentWriterInterface $molliePaymentWriter
     * @param \Mollie\Zed\Mollie\MollieConfig $config
     */
    public function __construct(
        protected MollieClientInterface $mollieClient,
        protected MollieToStorageClientInterface $storageClient,
        protected MolliePaymentWriterInterface $molliePaymentWriter,
        protected MollieConfig $config,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function createPayment(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): CheckoutResponseTransfer
    {
        $mollieApiRequestTransfer = (new MollieApiRequestTransfer())
            ->setCheckoutResponse($checkoutResponseTransfer)
            ->setQuote($quoteTransfer);

        $molliePaymentApiResponseTransfer = $this->mollieClient->createPayment($mollieApiRequestTransfer);

        if (!$molliePaymentApiResponseTransfer->getIsSuccessful()) {
            $this->getLogger()->error($molliePaymentApiResponseTransfer->getMessage());
            $errors = $this->createErrorMessages($molliePaymentApiResponseTransfer->getMessage());
            $redirectUrl = $this->getFailedPaymentRedirectUrl($checkoutResponseTransfer->getSaveOrder()->getOrderReference());
            $checkoutResponseTransfer
                ->setErrors($errors)
                ->setIsSuccess(false)
                ->setIsExternalRedirect(true)
                ->setRedirectUrl($redirectUrl);

            return $checkoutResponseTransfer;
        }

        $this->savePaymentIdToStorage($molliePaymentApiResponseTransfer->getMolliePayment()->getId(), $checkoutResponseTransfer->getSaveOrderOrFail()->getOrderReference());
        $this->molliePaymentWriter->addMolliePaymentData($checkoutResponseTransfer->getSaveOrder()->getIdSalesOrder(), $molliePaymentApiResponseTransfer->getMolliePayment());
        $redirectUrl = $this->getSuccessfulPaymentRedirectUrl($molliePaymentApiResponseTransfer);

        return $checkoutResponseTransfer
            ->setIsSuccess(true)
            ->setIsExternalRedirect(true)
            ->setRedirectUrl($redirectUrl);
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentApiResponseTransfer $molliePaymentApiResponseTransfer
     *
     * @return string|null
     */
    protected function getSuccessfulPaymentRedirectUrl(MolliePaymentApiResponseTransfer $molliePaymentApiResponseTransfer): ?string
    {
        return $molliePaymentApiResponseTransfer
            ->getMolliePayment()
            ->getLinks()[MollieConstants::RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS_CHECKOUT][MollieConstants::RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS_HREF] ?? null;
    }

    /**
     * @param string $orderReference
     *
     * @return string
     */
    protected function getFailedPaymentRedirectUrl(string $orderReference): string
    {
        return $this->config->getMollieRedirectUrl() . '?orderReference=' . $orderReference;
    }

    /**
     * @param string $paymentId
     * @param string $orderReference
     *
     * @return void
     */
    protected function savePaymentIdToStorage(string $paymentId, string $orderReference): void
    {
        $key = sprintf('%s:%s', MollieConstants::MOLLIE_PAYMENT_TRANSACTION_STORAGE_KEY_PREFIX, $orderReference);
        $this->storageClient->set($key, $paymentId, MollieConstants::MOLLIE_PAYMENT_TRANSACTION_STORAGE_TTL);
    }

    /**
     * @param string $message
     *
     * @return \ArrayObject<int, \Generated\Shared\Transfer\CheckoutErrorTransfer>
     */
    protected function createErrorMessages(string $message): ArrayObject
    {
        $error = new CheckoutErrorTransfer();
        $error->setMessage($message);

        $errors = new ArrayObject();
        $errors->append($error);

        return $errors;
    }
}
