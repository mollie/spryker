<?php

namespace Mollie\Client\Mollie\Api\PaymentMethods;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\OrderCollectionRequestTransfer;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\GetPaymentRequest;
use Mollie\Client\Mollie\Api\AbstractApiCall;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Shared\Log\LoggerTrait;

class GetPaymentById extends AbstractApiCall
{
    use LoggerTrait;

    /**
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer
     *
     * @return \Generated\Shared\Transfer\OrderCollectionRequestTransfer
     */
    protected function formatApiResponse(MollieApiResponseTransfer $mollieApiResponseTransfer): AbstractTransfer
    {
        $updateOrderCollectionTransfer = new OrderCollectionRequestTransfer();

        $updateOrderCollectionTransfer
            ->setId($mollieApiResponseTransfer->getPayload()['id'])
            ->setStatus($mollieApiResponseTransfer->getPayload()['status'])
            ->setIsSuccess($mollieApiResponseTransfer->getIsSuccessful());

        return $updateOrderCollectionTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer|null $mollieApiRequestTransfer
     *
     * @return \Mollie\Api\Http\Request|null
     */
    protected function buildRequest(?MollieApiRequestTransfer $mollieApiRequestTransfer = null): ?Request
    {
        return new GetPaymentRequest(
            $mollieApiRequestTransfer['body']['id'],
        );
    }
}
