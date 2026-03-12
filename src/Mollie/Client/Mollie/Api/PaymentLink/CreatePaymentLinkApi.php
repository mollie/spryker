<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Api\PaymentLink;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\CreatePaymentLinkRequest;
use Mollie\Api\MollieApiClient;
use Mollie\Client\Mollie\Api\AbstractApiCall;
use Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Mollie\Client\Mollie\Logger\MollieLoggerInterface;
use Mollie\Client\Mollie\MollieConfig;
use Mollie\Service\Mollie\MollieServiceInterface;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

class CreatePaymentLinkApi extends AbstractApiCall
{
    /**
     * @param MollieApiClient $mollieApiClient
     * @param MollieConfig $mollieConfig
     * @param MollieToUtilEncodingServiceInterface $utilEncodingService
     * @param MollieLoggerInterface $logger
     * @param MollieServiceInterface $mollieService
     */
    public function __construct(
        MollieApiClient $mollieApiClient,
        MollieConfig $mollieConfig,
        MollieToUtilEncodingServiceInterface $utilEncodingService,
        MollieLoggerInterface $logger,
        protected MollieServiceInterface $mollieService
    )
    {
        parent::__construct($mollieApiClient, $mollieConfig, $utilEncodingService, $logger);
    }

    /**
     * @param MollieApiRequestTransfer|null $mollieApiRequestTransfer
     * @return Request|null
     */
    protected function buildRequest(?MollieApiRequestTransfer $mollieApiRequestTransfer = null): ?Request
    {
        $checkoutResponseTransfer = $mollieApiRequestTransfer->getCheckoutResponse();

        $description = 'Test Payment'; // TODO: Get actual value from BO
        $redirectUrl = $this->getRedirectUrl($checkoutResponseTransfer->getSaveOrderOrFail()->getOrderReference());
        $webhookUrl = $this->mollieService->resolveWebhookUrl(
            $this->mollieConfig->getMollieWebhookUrl(),
            $this->mollieConfig->getTestEnvironmentMollieWebhookUrl(),
            $this->mollieConfig->isMollieTestModeEnabled(),
        );

        $amount = null; // TODO: Get actual value from BO
        $profileId = $this->mollieConfig->getMollieProfileId();
        $reusable = false; //TODO: Get actual value from BO
        $allowedMethods = []; //TODO: Get actual value from BO

        $this->request = new CreatePaymentLinkRequest(
            description: $description,
            amount: $amount,
            redirectUrl: $redirectUrl,
            webhookUrl: $webhookUrl,
            profileId: $profileId,
            reusable: $reusable,
            allowedMethods: $allowedMethods,
        );

        return $this->request;
    }

    /**
     * @param MollieApiResponseTransfer $mollieApiResponseTransfer
     * @return AbstractTransfer
     */
    protected function mapApiResponse(MollieApiResponseTransfer $mollieApiResponseTransfer): AbstractTransfer
    {
        $molliePaymentLinksApiResponseTransfer = (new MolliePaymentApiResponseTransfer())
            ->setIsSuccessful($mollieApiResponseTransfer->getIsSuccessful())
            ->setMessage($mollieApiResponseTransfer->getMessage());

        $molliePaymentTransfer = new MolliePaymentTransfer();
        $molliePaymentTransfer->fromArray($mollieApiResponseTransfer->getPayload(), true);

        $links = $mollieApiResponseTransfer->getPayload()[MollieConfig::RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS] ?? [];
        $mollieLinksTransfer = new MollieLinksTransfer();
        $mollieLinksTransfer->fromArray($links, true);
        $molliePaymentTransfer
            ->setLinks($mollieLinksTransfer);

        $molliePaymentLinksApiResponseTransfer->setMolliePayment($molliePaymentTransfer);

        return $molliePaymentLinksApiResponseTransfer;
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
}