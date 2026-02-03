<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Api\Profile;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\MollieGetProfileApiResponseTransfer;
use Generated\Shared\Transfer\MollieLogApiTransfer;
use Generated\Shared\Transfer\MollieProfileTransfer;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\GetCurrentProfileRequest;
use Mollie\Client\Mollie\Api\AbstractApiCall;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

class GetCurrentProfileApi extends AbstractApiCall
{
    protected Request|null $request;

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer|null $mollieApiRequestTransfer
     *
     * @return \Mollie\Api\Http\Request|null
     */
    public function buildRequest(?MollieApiRequestTransfer $mollieApiRequestTransfer = null): ?Request
    {
        $this->request = new GetCurrentProfileRequest();

        return $this->request;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer
     */
    protected function mapApiResponse(MollieApiResponseTransfer $mollieApiResponseTransfer): AbstractTransfer
    {
        $payload = $mollieApiResponseTransfer->getPayload();
        $molliePaymentMethodsApiResponseTransfer = new MollieGetProfileApiResponseTransfer();
        $molliePaymentMethodsApiResponseTransfer
            ->setIsSuccessful($mollieApiResponseTransfer->getIsSuccessful())
            ->setMessage($mollieApiResponseTransfer->getMessage())
            ->setProfile((new MollieProfileTransfer())->fromArray($payload, true));

        return $molliePaymentMethodsApiResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer
     *
     * @return \Generated\Shared\Transfer\MollieLogApiTransfer
     */
    protected function mapApiResponseToLogResponseTransfer(MollieApiResponseTransfer $mollieApiResponseTransfer): MollieLogApiTransfer
    {
        $fieldsToMaskForResponsePayload = [
            'id',
            'name',
            'email',
            'phone',
        ];

        $maskedResponseBody = $this->maskPayload($fieldsToMaskForResponsePayload, $mollieApiResponseTransfer->getPayload());

        return (new MollieLogApiTransfer())
            ->setIsSuccessful($mollieApiResponseTransfer->getIsSuccessful())
            ->setRequestIdentifier($this->getCorrelationId())
            ->setUrl($this->buildUrl())
            ->setRequestBody($this->getRequestBody())
            ->setPayload($maskedResponseBody)
            ->setCode($mollieApiResponseTransfer->getCode())
            ->setMessage($mollieApiResponseTransfer->getMessage());
    }
}
