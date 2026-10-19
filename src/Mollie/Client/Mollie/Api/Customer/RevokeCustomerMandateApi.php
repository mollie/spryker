<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Api\Customer;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\MollieMandateApiResponseTransfer;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\RevokeMandateRequest;
use Mollie\Client\Mollie\Api\AbstractApiCall;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

class RevokeCustomerMandateApi extends AbstractApiCall
{
    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer|null $mollieApiRequestTransfer
     *
     * @return \Mollie\Api\Http\Request|null
     */
    protected function buildRequest(?MollieApiRequestTransfer $mollieApiRequestTransfer = null): ?Request
    {
        $this->request = new RevokeMandateRequest(
            $mollieApiRequestTransfer->getMollieCustomerIdOrFail(),
            $mollieApiRequestTransfer->getMandateIdOrFail(),
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
        return (new MollieMandateApiResponseTransfer())
            ->setIsSuccessful($mollieApiResponseTransfer->getIsSuccessful())
            ->setCode($mollieApiResponseTransfer->getCode())
            ->setMessage($mollieApiResponseTransfer->getMessage());
    }
}
