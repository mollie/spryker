<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie\Api\Payment;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\ReleasePaymentAuthorizationRequest;
use Mollie\Client\Mollie\Api\AbstractApiCall;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

class ReleasePaymentAuthorizationApi extends AbstractApiCall
{
    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer|null $mollieApiRequestTransfer
     *
     * @return \Mollie\Api\Http\Request|null
     */
    protected function buildRequest(?MollieApiRequestTransfer $mollieApiRequestTransfer = null): ?Request
    {
        $transactionId = $mollieApiRequestTransfer->getTransactionId();

        $this->request = new ReleasePaymentAuthorizationRequest(
            paymentId: $transactionId,
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
        return $mollieApiResponseTransfer;
    }
}
