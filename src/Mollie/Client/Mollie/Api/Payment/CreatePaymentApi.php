<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie\Api\Payment;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\MollieLinksTransfer;
use Generated\Shared\Transfer\MolliePaymentApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Api\MollieApiClient;
use Mollie\Client\Mollie\Api\AbstractApiCall;
use Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Mollie\Client\Mollie\Handler\PaymentApiHandlerInterface;
use Mollie\Client\Mollie\Logger\MollieLoggerInterface;
use Mollie\Client\Mollie\MollieConfig;
use Mollie\Service\Mollie\MollieServiceInterface;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Shared\Log\LoggerTrait;

class CreatePaymentApi extends AbstractApiCall
{
    use LoggerTrait;

    /**
     * @param \Mollie\Api\MollieApiClient $mollieApiClient
     * @param \Mollie\Client\Mollie\MollieConfig $mollieConfig
     * @param \Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface $utilEncodingService
     * @param \Mollie\Client\Mollie\Logger\MollieLoggerInterface $logger
     * @param \Mollie\Service\Mollie\MollieServiceInterface $mollieService
     * @param \Mollie\Client\Mollie\Handler\PaymentApiHandlerInterface $apiHandler
     */
    public function __construct(
        MollieApiClient $mollieApiClient,
        MollieConfig $mollieConfig,
        MollieToUtilEncodingServiceInterface $utilEncodingService,
        MollieLoggerInterface $logger,
        protected MollieServiceInterface $mollieService,
        protected PaymentApiHandlerInterface $apiHandler,
    ) {
        parent::__construct($mollieApiClient, $mollieConfig, $utilEncodingService, $logger);
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

        $value = $this->convertAmountToString($paymentTransfer->getAmount());
        $amount = new Money(
            currency: $quoteTransfer->getCurrency()->getCode(),
            value: $value,
        );
        $redirectUrl = $this->getRedirectUrl($checkoutResponseTransfer->getSaveOrderOrFail()->getOrderReference());

        $webhookUrl = $this->mollieService->resolveWebhookUrl(
            $this->mollieConfig->getMollieWebhookUrl(),
            $this->mollieConfig->getTestEnvironmentMollieWebhookUrl(),
            $this->mollieConfig->isMollieTestModeEnabled(),
        );

        $method = $this->mollieConfig->getMolliePaymentMethod($paymentTransfer->getPaymentMethod());
        $metadata = $this->apiHandler->createPaymentMetadata($checkoutResponseTransfer);
        $additionalParameters = $this->apiHandler->createAdditionalParameters($mollieApiRequestTransfer);
        $billingAddress = $this->apiHandler->createBillingAddress($quoteTransfer);
        $lines = $this->apiHandler->createLines($quoteTransfer, $method);

        $this->request = new CreatePaymentRequest(
            description: $checkoutResponseTransfer->getSaveOrderOrFail()->getOrderReference(),
            amount: $amount,
            redirectUrl: $redirectUrl,
            webhookUrl: $webhookUrl,
            lines: $lines,
            billingAddress: $billingAddress,
            method: $method,
            metadata: $metadata,
            captureMode: $this->getCaptureModeForMethod($method),
            additional: $additionalParameters,
        );

        return $this->request;
    }

    /**
     * @param int $amount
     *
     * @return string
     */
    protected function convertAmountToString(int $amount): string
    {
        $mollieAmountTransfer = $this->mollieService->convertIntegerToMollieAmount($amount);

        return $mollieAmountTransfer->getValue();
    }

    /**
     * @param string $orderReference
     *
     * @return string
     */
    protected function getRedirectUrl(string $orderReference): string
    {
        return $this->mollieConfig->getMollieRedirectUrl() . '?orderReference=' . $orderReference;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    protected function mapApiResponse(MollieApiResponseTransfer $mollieApiResponseTransfer): AbstractTransfer
    {
        $molliePaymentApiResponseTransfer = (new MolliePaymentApiResponseTransfer())
            ->setIsSuccessful($mollieApiResponseTransfer->getIsSuccessful())
            ->setMessage($mollieApiResponseTransfer->getMessage());

        $molliePaymentTransfer = new MolliePaymentTransfer();
        $molliePaymentTransfer->fromArray($mollieApiResponseTransfer->getPayload(), true);

        $links = $mollieApiResponseTransfer->getPayload()[MollieConfig::RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS] ?? [];
        $mollieLinksTransfer = new MollieLinksTransfer();
        $mollieLinksTransfer->fromArray($links, true);
        $molliePaymentTransfer
            ->setLinks($mollieLinksTransfer);

        $molliePaymentApiResponseTransfer->setMolliePayment($molliePaymentTransfer);

        return $molliePaymentApiResponseTransfer;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getRequestBody(): array
    {
         /** @var \Mollie\Api\Http\Requests\CreatePaymentRequest|null $createPaymentRequest */
        $createPaymentRequest = $this->request;

        if (!$createPaymentRequest) {
            return [];
        }

        $payload = $createPaymentRequest->payload();
        $requestBody = $payload->all();

        return $requestBody;
    }

    /**
     * @param string $method
     *
     * @return string
     */
    protected function getCaptureModeForMethod(string $method): string
    {
        $molliePaymentMethodsManualCapture = $this->mollieConfig->getMolliePaymentMethodsManualCapture();
        if (in_array($method, $molliePaymentMethodsManualCapture)) {
            return $this->mollieConfig->getMollieManualCaptureMode();
        }

        return $this->mollieConfig->getMollieAutomaticCaptureMode();
    }
}
