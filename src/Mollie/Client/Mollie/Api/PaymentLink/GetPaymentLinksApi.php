<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Api\PaymentLink;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentApiResponseTransfer;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\GetPaginatedPaymentLinksRequest;
use Mollie\Client\Mollie\Api\AbstractApiCall;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

class GetPaymentLinksApi extends AbstractApiCall
{

    /**
     * @param MollieApiRequestTransfer|null $mollieApiRequestTransfer
     * @return Request|null
     */
    protected function buildRequest(?MollieApiRequestTransfer $mollieApiRequestTransfer = null): ?Request
    {
        $this->request = new GetPaginatedPaymentLinksRequest(
            from: null,
            limit: 10,
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
        
        return $molliePaymentLinksApiResponseTransfer;
    }
}