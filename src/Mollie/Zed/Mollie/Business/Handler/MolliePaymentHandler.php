<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Handler;

use ArrayObject;
use Exception;
use Generated\Shared\Transfer\CheckoutErrorTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Shared\Mollie\MollieConfig as MollieMollieConfig;
use Mollie\Zed\Mollie\Business\Exception\MolliePaymentException;
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
     */
    public function __construct(
        protected MollieClientInterface $mollieClient,
        protected MollieToStorageClientInterface $storageClient,
        protected MolliePaymentWriterInterface $molliePaymentWriter,
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

            $molliePaymentApiResponseTransfer = $this->mollieClient->createPayment($mollieApiRequestTransfer);

            if (!$molliePaymentApiResponseTransfer->getIsSuccessful()) {
                $this->getLogger()->error($molliePaymentApiResponseTransfer->getMessage());
                $errors = $this->createErrorMessages();
                $checkoutResponseTransfer
                    ->setErrors($errors)
                    ->setIsSuccess(false);

                return $checkoutResponseTransfer;
            }

            $this->savePaymentIdToStorage($molliePaymentApiResponseTransfer->getMolliePayment()->getId(), $checkoutResponseTransfer->getSaveOrderOrFail()->getOrderReference());
            $this->molliePaymentWriter->addMolliePaymentData($checkoutResponseTransfer->getSaveOrder()->getIdSalesOrder(), $molliePaymentApiResponseTransfer->getMolliePayment());
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
            ->setRedirectUrl($molliePaymentApiResponseTransfer->getMolliePayment()->getLinks()[MollieConfig::RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS_CHECKOUT][MollieConfig::RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS_HREF] ?? null);
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

    /**
     * @return \ArrayObject<int, \Generated\Shared\Transfer\CheckoutErrorTransfer>
     */
    protected function createErrorMessages(): ArrayObject
    {
        $error = new CheckoutErrorTransfer();
        $error->setMessage(static::DEFAULT_ERROR_MESSAGE);

        $errors = new ArrayObject();
        $errors->append($error);

        return $errors;
    }
}
