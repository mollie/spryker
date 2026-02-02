<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie\Api\Payment;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\MollieLogApiTransfer;
use Generated\Shared\Transfer\MolliePaymentApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\GetPaymentRequest;
use Mollie\Client\Mollie\Api\AbstractApiCall;
use Mollie\Shared\Mollie\MollieConstants;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Shared\Log\LoggerTrait;

class GetPaymentByTransactionIdApi extends AbstractApiCall
{
    use LoggerTrait;

    /**
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentApiResponseTransfer
     */
    protected function mapApiResponse(MollieApiResponseTransfer $mollieApiResponseTransfer): AbstractTransfer
    {
        $molliePaymentApiResponseTransfer = (new MolliePaymentApiResponseTransfer())
            ->setIsSuccessful($mollieApiResponseTransfer->getIsSuccessful())
            ->setMessage(MollieConstants::SUCCESS_MESSAGE);

        $molliePaymentTransfer = (new MolliePaymentTransfer())
            ->fromArray($mollieApiResponseTransfer->getPayload(), true);

        $molliePaymentApiResponseTransfer->setMolliePayment($molliePaymentTransfer);

        return $molliePaymentApiResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieLogApiTransfer $mollieLogApiTransfer
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer
     *
     * @return \Generated\Shared\Transfer\MollieLogApiTransfer
     */
    protected function expandApiLogTransfer(
        MollieLogApiTransfer $mollieLogApiTransfer,
        MollieApiResponseTransfer $mollieApiResponseTransfer,
    ): MollieLogApiTransfer {
        /** @var \Mollie\Api\Http\Requests\GetPaymentRequest $getPaymentRequest */
        $getPaymentRequest = $this->request;
        $requestBody = $getPaymentRequest->query()->all();
        $payload = $mollieApiResponseTransfer->getPayload();

        $payload['id'] = static::MASKED;
        $payload['description'] = static::MASKED;
        $payload['metadata']['order_id'] = static::MASKED;

        return $mollieLogApiTransfer
            ->setUrl($this->buildUrl())
            ->setRequestBody($requestBody)
            ->setPayload($payload);
    }

    /**
     * @return string
     */
    protected function buildUrl(): string
    {
        return sprintf(
            static::URL_FORMAT,
            $this->mollieApiClient->resolveBaseUrl(),
            static::MASKED,
        );
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer|null $mollieApiRequestTransfer
     *
     * @return \Mollie\Api\Http\Request|null
     */
    protected function buildRequest(?MollieApiRequestTransfer $mollieApiRequestTransfer = null): ?Request
    {
        $this->request = new GetPaymentRequest($mollieApiRequestTransfer->getTransactionId());

        return $this->request;
    }
}
