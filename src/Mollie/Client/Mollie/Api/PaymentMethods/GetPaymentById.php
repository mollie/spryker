<?php

namespace Mollie\Client\Mollie\Api\PaymentMethods;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\OrderCollectionRequestTransfer;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\GetPaymentRequest;
use Mollie\Client\Mollie\Api\AbstractApiCall;
use Mollie\Client\Mollie\Api\Exception\GetPaymentByIdException;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Shared\Log\LoggerTrait;

class GetPaymentById extends AbstractApiCall
{
    use LoggerTrait;

    /**
     * @param \Mollie\Api\Http\Request $request
     *
     * @throws \Mollie\Client\Mollie\Api\Exception\GetPaymentByIdException
     *
     * @return \Generated\Shared\Transfer\MollieApiResponseTransfer
     */
    protected function send(Request $request): MollieApiResponseTransfer
    {
        try {
            $payment = $this->mollieApiClient->send($request);

            $mollieApiResponseTransfer = new MollieApiResponseTransfer();

            $mollieApiResponseTransfer
                ->setIsSuccessful($payment->id !== null)
                ->setPayload(json_decode($payment->getResponse()->body(), true))
                ->setMessage('OK')
                ->setCode(200);

            return $mollieApiResponseTransfer;
        } catch (ApiException $requestException) {
            $logException = sprintf(
                'Error calling get payment by id api method with message: %s',
                $requestException->getMessage(),
            );

            $this->getLogger()->error($logException);

            throw new GetPaymentByIdException(
                $requestException->getMessage(),
                $requestException->getCode(),
            );
        }
    }

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
            ->setpaidAt($mollieApiResponseTransfer->getPayload()['paidAt'])
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
