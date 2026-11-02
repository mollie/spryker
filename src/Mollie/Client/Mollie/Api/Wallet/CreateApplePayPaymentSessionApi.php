<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie\Api\Wallet;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\MollieApplePayPaymentSessionApiResponseTransfer;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\ApplePayPaymentSessionRequest;
use Mollie\Client\Mollie\Api\AbstractApiCall;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

class CreateApplePayPaymentSessionApi extends AbstractApiCall
{
    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer|null $mollieApiRequestTransfer
     *
     * @return \Mollie\Api\Http\Request|null
     */
    protected function buildRequest(?MollieApiRequestTransfer $mollieApiRequestTransfer = null): ?Request
    {
        $applePayDomain = $mollieApiRequestTransfer->getApplePayDomainOrFail();
        $applePayValidationUrl = $mollieApiRequestTransfer->getApplePayValidationUrlOrFail();

        $this->request = new ApplePayPaymentSessionRequest(
            domain: $applePayDomain,
            validationUrl: $applePayValidationUrl,
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
        $mollieApplePayPaymentSessionApiResponseTransfer = new MollieApplePayPaymentSessionApiResponseTransfer();
        $mollieApplePayPaymentSessionApiResponseTransfer
            ->setIsSuccessful($mollieApiResponseTransfer->getIsSuccessful())
            ->setMessage($mollieApiResponseTransfer->getMessage());

        if (!$mollieApiResponseTransfer->getIsSuccessful()) {
            return $mollieApplePayPaymentSessionApiResponseTransfer;
        }

        $applePayPaymentSession = $mollieApiResponseTransfer->getPayload();
        $mollieApplePayPaymentSessionApiResponseTransfer->setApplePayPaymentSession($applePayPaymentSession);

        return $mollieApplePayPaymentSessionApiResponseTransfer;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getRequestBody(): array
    {
        /** @var \Mollie\Api\Http\Requests\ApplePayPaymentSessionRequest|null $applePayPaymentSessionRequest */
        $applePayPaymentSessionRequest = $this->request;

        if (!$applePayPaymentSessionRequest) {
            return [];
        }

        $payload = $applePayPaymentSessionRequest->payload();
        $requestBody = $payload->all();

        return $requestBody;
    }
}
