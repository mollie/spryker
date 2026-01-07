<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Api\Payment;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Exceptions\MollieException;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\GetPaymentRequest;
use Mollie\Api\MollieApiClient;
use Mollie\Client\Mollie\Api\AbstractApiCall;
use Mollie\Client\Mollie\Api\Exception\GetPaymentApiException;
use Mollie\Client\Mollie\Mapper\MollieApiResponseMapperInterface;
use Mollie\Client\Mollie\MollieConfig;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Shared\Log\LoggerTrait;

class GetPaymentApi extends AbstractApiCall
{
    use LoggerTrait;

    /**
     * @param \Mollie\Api\MollieApiClient $mollieApiClient
     * @param \Mollie\Client\Mollie\Mapper\MollieApiResponseMapperInterface $paymentMapper
     * @param \Mollie\Client\Mollie\MollieConfig $config
     */
    public function __construct(
        MollieApiClient $mollieApiClient,
        protected MollieApiResponseMapperInterface $paymentMapper,
        protected MollieConfig $config,
    ) {
        parent::__construct($mollieApiClient);
    }

    /**
     * @param \Mollie\Api\Http\Request $request
     *
     * @throws \Mollie\Client\Mollie\Api\Exception\GetPaymentApiException
     *
     * @return \Generated\Shared\Transfer\MollieApiResponseTransfer
     */
    protected function send(Request $request): MollieApiResponseTransfer
    {
        try {
            $this->mollieApiClient->setApiKey($this->config->getMollieApiKey());
            $payment = $this->mollieApiClient->send($request);

            $mollieApiResponseTransfer = new MollieApiResponseTransfer();
            $mollieApiResponseTransfer
                ->setIsSuccessful(true)
                ->setPayload($payment->getResponse()->getPsrResponse()->getBody()->getContents());

            return $mollieApiResponseTransfer;
        } catch (ApiException | MollieException $exception) {
            $logException = sprintf(
                'Error calling get payment API with message: %s',
                $exception->getMessage(),
            );

            $this->getLogger()->error($logException);

            throw new GetPaymentApiException(
                $exception->getMessage(),
                $exception->getCode(),
            );
        }
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    protected function formatApiResponse(MollieApiResponseTransfer $mollieApiResponseTransfer): AbstractTransfer
    {
        return $this->paymentMapper->mapPayloadToResponseTransfer($mollieApiResponseTransfer->getPayload());
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer|null $mollieApiRequestTransfer
     *
     * @return \Mollie\Api\Http\Request|null
     */
    protected function buildRequest(?MollieApiRequestTransfer $mollieApiRequestTransfer = null): ?Request
    {
        return new GetPaymentRequest(
            $mollieApiRequestTransfer->getPaymentId(),
        );
    }
}
