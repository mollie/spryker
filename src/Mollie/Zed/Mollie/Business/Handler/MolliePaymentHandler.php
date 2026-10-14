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
use Mollie\Zed\Mollie\Business\SingleClick\SingleClickPaymentResolver;
use Mollie\Zed\Mollie\Business\SingleClick\SingleClickPaymentResolverInterface;
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
     * @param \Mollie\Zed\Mollie\Business\SingleClick\SingleClickPaymentResolverInterface $singleClickPaymentResolver
     */
    public function __construct(
        protected MollieClientInterface $mollieClient,
        protected MollieToStorageClientInterface $storageClient,
        protected MolliePaymentWriterInterface $molliePaymentWriter,
        protected MollieConfig $config,
        protected SingleClickPaymentResolverInterface $singleClickPaymentResolver,
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
        $orderReference = $checkoutResponseTransfer->getSaveOrderOrFail()->getOrderReferenceOrFail();
        $quoteTransfer = $this->singleClickPaymentResolver->resolve($quoteTransfer, $orderReference);

        $molliePaymentApiResponseTransfer = $this->requestPayment($quoteTransfer, $checkoutResponseTransfer);

        if (!$molliePaymentApiResponseTransfer->getIsSuccessful() && $this->singleClickPaymentResolver->isOneClickPayment($quoteTransfer)) {
            $this->getLogger()->warning('Mollie single-click: one-click payment could not be created, retrying with the full checkout.', [
                'orderReference' => $orderReference,
                'message' => $molliePaymentApiResponseTransfer->getMessage(),
            ]);
            $quoteTransfer = $this->singleClickPaymentResolver->applyFallback(
                $quoteTransfer,
                $orderReference,
                SingleClickPaymentResolver::REASON_ONE_CLICK_PAYMENT_FAILED,
            );
            $molliePaymentApiResponseTransfer = $this->requestPayment($quoteTransfer, $checkoutResponseTransfer);
        }

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

        $this->savePaymentIdToStorage($molliePaymentApiResponseTransfer->getMolliePayment()->getId(), $orderReference);
        $this->molliePaymentWriter->addMolliePaymentData($checkoutResponseTransfer->getSaveOrder()->getIdSalesOrder(), $molliePaymentApiResponseTransfer->getMolliePayment());
        $redirectUrl = $this->getSuccessfulPaymentRedirectUrl($molliePaymentApiResponseTransfer, $orderReference);

        return $checkoutResponseTransfer
            ->setIsSuccess(true)
            ->setIsExternalRedirect(true)
            ->setRedirectUrl($redirectUrl);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentApiResponseTransfer
     */
    protected function requestPayment(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer,
    ): MolliePaymentApiResponseTransfer {
        $mollieApiRequestTransfer = (new MollieApiRequestTransfer())
            ->setCheckoutResponse($checkoutResponseTransfer)
            ->setQuote($quoteTransfer);

        return $this->mollieClient->createPayment($mollieApiRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentApiResponseTransfer $molliePaymentApiResponseTransfer
     * @param string $orderReference
     *
     * @return string
     */
    protected function getSuccessfulPaymentRedirectUrl(
        MolliePaymentApiResponseTransfer $molliePaymentApiResponseTransfer,
        string $orderReference,
    ): string {
        $molliePaymentTransfer = $molliePaymentApiResponseTransfer->getMolliePayment();
        $mollieLinksTransfer = $molliePaymentTransfer->getLinks();
        $checkoutUrl = $mollieLinksTransfer[MollieConstants::RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS_CHECKOUT][MollieConstants::RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS_HREF] ?? null;

        if ($checkoutUrl) {
            return $checkoutUrl;
        }

        return $this->getMolliePaymentRedirectUrl($orderReference);
    }

    /**
     * @param string $orderReference
     *
     * @return string
     */
    protected function getFailedPaymentRedirectUrl(string $orderReference): string
    {
        return $this->getMolliePaymentRedirectUrl($orderReference);
    }

    /**
     * @param string $orderReference
     *
     * @return string
     */
    protected function getMolliePaymentRedirectUrl(string $orderReference): string
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
