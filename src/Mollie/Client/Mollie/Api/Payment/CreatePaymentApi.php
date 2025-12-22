<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Api\Payment;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Exceptions\MollieException;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Api\MollieApiClient;
use Mollie\Client\Mollie\Api\AbstractApiCall;
use Mollie\Client\Mollie\Api\Exception\CreatePaymentApiException;
use Mollie\Client\Mollie\Mapper\MollieApiResponseMapperInterface;
use Mollie\Client\Mollie\MollieConfig;
use Mollie\Client\Mollie\Storage\MolliePaymentStorageSaverInterface;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Shared\Log\LoggerTrait;

class CreatePaymentApi extends AbstractApiCall
{
    use LoggerTrait;

    /**
     * @param \Mollie\Api\MollieApiClient $mollieApiClient
     * @param \Mollie\Client\Mollie\Mapper\MollieApiResponseMapperInterface $paymentMapper
     * @param \Mollie\Client\Mollie\Storage\MolliePaymentStorageSaverInterface $storageSaver
     * @param \Mollie\Client\Mollie\MollieConfig $config
     */
    public function __construct(
        MollieApiClient $mollieApiClient,
        protected MollieApiResponseMapperInterface $paymentMapper,
        protected MolliePaymentStorageSaverInterface $storageSaver,
        protected MollieConfig $config,
    ) {
        parent::__construct($mollieApiClient);
    }

    /**
     * @param \Mollie\Api\Http\Request $request
     *
     * @throws \Mollie\Client\Mollie\Api\Exception\CreatePaymentApiException
     *
     * @return \Generated\Shared\Transfer\MollieApiResponseTransfer
     */
    protected function send(Request $request): MollieApiResponseTransfer
    {
        try {
            $this->mollieApiClient->setApiKey($this->config->getMollieApiKey());
            $payment = $this->mollieApiClient->send($request);

            $mollieApiResponseTransfer = new MollieApiResponseTransfer();
            $mollieApiResponseTransfer
                ->setIsSuccessful(true)
                ->setPayload($this->paymentMapper->mapDataToArray($payment));

            $this->savePaymentIdToStorage($mollieApiResponseTransfer->getPayload());

            return $mollieApiResponseTransfer;
        } catch (ApiException | MollieException $requestException) {
            $logException = sprintf(
                'Error calling create payment api with message: %s',
                $requestException->getMessage(),
            );

            $this->getLogger()->error($logException);

            throw new CreatePaymentApiException(
                $requestException->getMessage(),
                $requestException->getCode(),
            );
        }
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    protected function formatApiResponse(MollieApiResponseTransfer $mollieApiResponseTransfer): AbstractTransfer
    {
        /** @var \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer */
        $molliePaymentTransfer = $this->paymentMapper->mapPayloadToResponseTransfer($mollieApiResponseTransfer->getPayload());

        return $molliePaymentTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer|null $mollieApiRequestTransfer
     *
     * @return \Mollie\Api\Http\Request|null
     */
    protected function buildRequest(?MollieApiRequestTransfer $mollieApiRequestTransfer = null): ?Request
    {
        $checkoutResponseTransfer = $mollieApiRequestTransfer->getCheckoutResponse();
        $quoteTransfer = $mollieApiRequestTransfer->getQuote();
        $paymentTransfer = $quoteTransfer->getPayment();

        return new CreatePaymentRequest(
            description: $checkoutResponseTransfer->getSaveOrderOrFail()->getOrderReference(),
            amount: new Money(
                currency: $quoteTransfer->getCurrency()->getCode(),
                value: $this->convertAmountToString($paymentTransfer->getAmount()),
            ),
            redirectUrl: $this->getRedirectUrl($checkoutResponseTransfer->getSaveOrderOrFail()->getOrderReference()),
            //webhookUrl: $this->config->getMollieWebhookUrl(),
            method: $this->config->getMolliePaymentMethod($paymentTransfer->getPaymentMethod()),
            metadata: $this->addMetadata($checkoutResponseTransfer),
            additional: $this->addAdditionalParameters($mollieApiRequestTransfer),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return array<string, string>
     */
    protected function addAdditionalParameters(MollieApiRequestTransfer $mollieApiRequestTransfer): array
    {
        $additionalData = [];
        $paymentTransfer = $mollieApiRequestTransfer->getQuote()->getPayment();
        if ($paymentTransfer->getMollieCreditCardPayment()) {
            $additionalData[MollieConfig::REQUEST_PARAMETER_CREATE_PAYMENT_CARD_TOKEN] = $paymentTransfer->getMollieCreditCardPayment()->getCardToken();
        }

        return $additionalData;
    }

    /**
     * @param int $amount
     *
     * @return string
     */
    protected function convertAmountToString(int $amount): string
    {
        return bcdiv((string)$amount, '100', 2);
    }

    /**
     * @param string $orderReference
     *
     * @return string
     */
    protected function getRedirectUrl(string $orderReference): string
    {
        return $this->config->getMollieRedirectUrl() . '?orderReference=' . $orderReference;
    }

    /**
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return array<string, mixed>
     */
    protected function addMetadata(CheckoutResponseTransfer $checkoutResponseTransfer): array
    {
        return [MollieConfig::REQUEST_PARAMETER_CREATE_PAYMENT_ORDER_REFERENCE => $checkoutResponseTransfer->getSaveOrderOrFail()->getOrderReference()];
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return void
     */
    protected function savePaymentIdToStorage(array $payload): void
    {
        $orderReference = $payload[MollieConfig::RESPONSE_PARAMETER_CREATE_PAYMENT_METADATA][MollieConfig::REQUEST_PARAMETER_CREATE_PAYMENT_ORDER_REFERENCE] ?? null;
        $paymentId = $payload['id'] ?? null;
        if (!$orderReference || !$paymentId) {
            return;
        }
        $this->storageSaver->savePaymentIdKey($orderReference, $paymentId);
    }
}
