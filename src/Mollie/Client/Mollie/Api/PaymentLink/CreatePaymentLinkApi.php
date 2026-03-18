<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Api\PaymentLink;

use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\MollieLinksTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Mollie\Api\Http\Data\Money;
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
     * @param \Mollie\Api\MollieApiClient $mollieApiClient
     * @param \Mollie\Client\Mollie\MollieConfig $mollieConfig
     * @param \Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface $utilEncodingService
     * @param \Mollie\Client\Mollie\Logger\MollieLoggerInterface $logger
     * @param \Mollie\Service\Mollie\MollieServiceInterface $mollieService
     */
    public function __construct(
        MollieApiClient $mollieApiClient,
        MollieConfig $mollieConfig,
        MollieToUtilEncodingServiceInterface $utilEncodingService,
        MollieLoggerInterface $logger,
        protected MollieServiceInterface $mollieService,
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
        $paymentLinkTransfer = $mollieApiRequestTransfer->getPaymentLink();

        $description = $paymentLinkTransfer->getDescription();
        $redirectUrl = $paymentLinkTransfer->getRedirectUrl();
        $webhookUrl = $this->mollieService->resolveWebhookUrl(
            $this->mollieConfig->getMollieWebhookUrl(),
            $this->mollieConfig->getTestEnvironmentMollieWebhookUrl(),
            $this->mollieConfig->isMollieTestModeEnabled(),
        );

        $amount = $this->convertMollieAmountTransferToMoney($paymentLinkTransfer->getAmount());
        $reusable = $paymentLinkTransfer->getReusable();
        $allowedMethods = $paymentLinkTransfer->getAllowedMethods();
        $expiresAt = $paymentLinkTransfer->getExpiresAt();

        $this->request = new CreatePaymentLinkRequest(
            description: $description,
            amount: $amount,
            redirectUrl: $redirectUrl,
            webhookUrl: $webhookUrl,
            reusable: $reusable,
            expiresAt: $expiresAt,
            allowedMethods: $allowedMethods,
        );

        return $this->request;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    protected function mapApiResponse(MollieApiResponseTransfer $mollieApiResponseTransfer): AbstractTransfer
    {
        $molliePaymentLinksApiResponseTransfer = (new MolliePaymentLinkApiResponseTransfer())
            ->setIsSuccessful($mollieApiResponseTransfer->getIsSuccessful())
            ->setMessage($mollieApiResponseTransfer->getMessage());

        if (!$mollieApiResponseTransfer->getIsSuccessful()) {
            return $molliePaymentLinksApiResponseTransfer;
        }

        $molliePaymentLinkTransfer = new MolliePaymentLinkTransfer();
        $molliePaymentLinkTransfer->fromArray($mollieApiResponseTransfer->getPayload(), true);

        $links = $mollieApiResponseTransfer->getPayload()[MollieConfig::RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS] ?? [];
        $mollieLinksTransfer = new MollieLinksTransfer();
        $mollieLinksTransfer->fromArray($links, true);

        $status = MollieConfig::RESPONSE_CREATE_PAYMENT_LINK_STATUS_OPEN;

        $molliePaymentLinkTransfer
            ->setStatus($status)
            ->setLinks($mollieLinksTransfer);

        $molliePaymentLinksApiResponseTransfer->setMolliePaymentLink($molliePaymentLinkTransfer);

        return $molliePaymentLinksApiResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieAmountTransfer $mollieAmountTransfer
     *
     * @return \Mollie\Api\Http\Data\Money
     */
    protected function convertMollieAmountTransferToMoney(MollieAmountTransfer $mollieAmountTransfer): Money
    {
        $money = new Money($mollieAmountTransfer->getCurrency(), $mollieAmountTransfer->getValue());

        return $money;
    }
}
