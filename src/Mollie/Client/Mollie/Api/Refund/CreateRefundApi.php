<?php

namespace Mollie\Client\Mollie\Api\Refund;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\MollieLinksTransfer;
use Generated\Shared\Transfer\MollieRefundApiResponseTransfer;
use Generated\Shared\Transfer\MollieRefundTransfer;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\CreatePaymentRefundRequest;
use Mollie\Api\MollieApiClient;
use Mollie\Client\Mollie\Api\AbstractApiCall;
use Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Mollie\Client\Mollie\Logger\MollieLoggerInterface;
use Mollie\Client\Mollie\MollieConfig;
use Mollie\Service\Mollie\MollieServiceInterface;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Shared\Log\LoggerTrait;

class CreateRefundApi extends AbstractApiCall
{
    use LoggerTrait;

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
        $orderItemsRefundableAmount = $mollieApiRequestTransfer->getRefund()->getAmount()->getValue();
        $convertedOrderItemsRefundableAmount = $this->convertAmountToString($orderItemsRefundableAmount);
        $currency = $mollieApiRequestTransfer->getRefund()->getAmount()->getCurrency();

        $amount = new Money(
            currency: $currency,
            value: $convertedOrderItemsRefundableAmount,
        );

        $this->request = new CreatePaymentRefundRequest(
            paymentId: $mollieApiRequestTransfer->getRefund()->getTransactionId(),
            description: $mollieApiRequestTransfer->getRefund()->getDescription(),
            amount: $amount,
            metadata: $mollieApiRequestTransfer->getRefund()->getMetadata(),
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
        $mollieRefundApiResponseTransfer = (new MollieRefundApiResponseTransfer())
            ->setIsSuccessful($mollieApiResponseTransfer->getIsSuccessful())
            ->setMessage($mollieApiResponseTransfer->getMessage());

        $mollieRefundTransfer = new MollieRefundTransfer();
        $mollieRefundTransfer->fromArray($mollieApiResponseTransfer->getPayload(), true);

        $links = $mollieApiResponseTransfer->getPayload()[MollieConfig::RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS] ?? [];
        $mollieLinksTransfer = new MollieLinksTransfer();
        $mollieLinksTransfer->fromArray($links, true);
        $mollieRefundTransfer
            ->setLinks($mollieLinksTransfer);

        $mollieRefundApiResponseTransfer->setMollieRefund($mollieRefundTransfer);

        return $mollieRefundApiResponseTransfer;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getRequestBody(): array
    {
        /** @var \Mollie\Api\Http\Requests\CreatePaymentRefundRequest $createPaymentRefundRequest */
        $createPaymentRefundRequest = $this->request;

        $payload = $createPaymentRefundRequest->payload();
        $requestBody = $payload->all();

        return $requestBody;
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
}
