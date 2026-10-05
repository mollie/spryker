<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie\Api\Session;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutSessionApiResponseTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutSessionTransfer;
use Generated\Shared\Transfer\MollieLinksTransfer;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\CreateSessionRequest;
use Mollie\Api\MollieApiClient;
use Mollie\Client\Mollie\Api\AbstractApiCall;
use Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Mollie\Client\Mollie\Handler\PaymentApiHandlerInterface;
use Mollie\Client\Mollie\Logger\MollieLoggerInterface;
use Mollie\Client\Mollie\MollieConfig;
use Mollie\Service\Mollie\MollieServiceInterface;
use Mollie\Shared\Mollie\MollieConfig as SharedConfig;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

class CreateExpressCheckoutSessionApi extends AbstractApiCall
{
    /**
     * @var string
     */
    protected const PAYLOAD_KEY_REQUIRED_CUSTOMER_DETAILS = 'requiredCustomerDetails';

    /**
     * Customer details Mollie collects in the express component and returns on the session's and payment's addresses.
     *
     * @var array<string>
     */
    protected const REQUIRED_CUSTOMER_DETAILS = ['email', 'billing-address', 'shipping-address'];

    /**
     * @var string
     */
    protected const PAYLOAD_KEY_SHIPPING = 'shipping';

    /**
     * @var string
     */
    protected const PAYLOAD_KEY_SHIPPING_CALLBACK_URL = 'callbackUrl';

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
        $quoteTransfer = $mollieApiRequestTransfer->getQuoteOrFail();
        $currencyCode = $quoteTransfer->getCurrency()->getCode();

        $value = $this->mollieService
            ->convertIntegerToMollieAmount($quoteTransfer->getTotals()->getGrandTotal(), $currencyCode)
            ->getValue();

        $amount = new Money(
            currency: $currencyCode,
            value: $value,
        );

        $lines = $this->apiHandler->createLines($quoteTransfer);

        $metadata = [
            SharedConfig::EXPRESS_CHECKOUT_METADATA_KEY_UUID => $mollieApiRequestTransfer->getExpressCheckoutUuidOrFail(),
        ];

        $webhookUrl = $this->mollieService->resolveWebhookUrl(
            $this->mollieConfig->getMollieWebhookUrl(),
            $this->mollieConfig->getTestEnvironmentMollieWebhookUrl(),
            $this->mollieConfig->isMollieTestModeEnabled(),
        );

        $this->request = new CreateSessionRequest(
            amount: $amount,
            description: $mollieApiRequestTransfer->getDescriptionOrFail(),
            redirectUrl: $mollieApiRequestTransfer->getRedirectUrlOrFail(),
            lines: $lines,
            metadata: $metadata,
            paymentWebhook: $webhookUrl,
        );

        // Not supported by the SDK's CreateSessionRequest yet, so it is added to the payload directly.
        $this->request->payload()->add(static::PAYLOAD_KEY_REQUIRED_CUSTOMER_DETAILS, static::REQUIRED_CUSTOMER_DETAILS);

        // Dynamic shipping options: Mollie asks this URL for the shipping options whenever the shopper picks
        // an address in the express sheet, and adds the chosen option to the amount. Not supported by the SDK yet.
        if ($mollieApiRequestTransfer->getShippingCallbackUrl()) {
            $this->request->payload()->add(static::PAYLOAD_KEY_SHIPPING, [
                static::PAYLOAD_KEY_SHIPPING_CALLBACK_URL => $mollieApiRequestTransfer->getShippingCallbackUrl(),
            ]);
        }

        return $this->request;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    protected function mapApiResponse(MollieApiResponseTransfer $mollieApiResponseTransfer): AbstractTransfer
    {
        $mollieExpressCheckoutSessionApiResponseTransfer = (new MollieExpressCheckoutSessionApiResponseTransfer())
            ->setIsSuccessful($mollieApiResponseTransfer->getIsSuccessful())
            ->setMessage($mollieApiResponseTransfer->getMessage());

        if (!$mollieApiResponseTransfer->getIsSuccessful()) {
            return $mollieExpressCheckoutSessionApiResponseTransfer;
        }

        $mollieExpressCheckoutSessionTransfer = new MollieExpressCheckoutSessionTransfer();
        $mollieExpressCheckoutSessionTransfer->fromArray($mollieApiResponseTransfer->getPayload(), true);

        $links = $mollieApiResponseTransfer->getPayload()[MollieConfig::RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS] ?? [];
        $mollieLinksTransfer = new MollieLinksTransfer();
        $mollieLinksTransfer->fromArray($links, true);
        $mollieExpressCheckoutSessionTransfer->setLinks($mollieLinksTransfer);

        $mollieExpressCheckoutSessionApiResponseTransfer->setExpressCheckoutSession($mollieExpressCheckoutSessionTransfer);

        return $mollieExpressCheckoutSessionApiResponseTransfer;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getRequestBody(): array
    {
        /** @var \Mollie\Api\Http\Requests\CreateSessionRequest|null $createSessionRequest */
        $createSessionRequest = $this->request;

        if (!$createSessionRequest) {
            return [];
        }

        return $createSessionRequest->payload()->all();
    }
}
