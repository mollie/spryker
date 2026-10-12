<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie\Api\Session;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutSessionApiResponseTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutSessionTransfer;
use Generated\Shared\Transfer\MollieLinksTransfer;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\GetSessionRequest;
use Mollie\Client\Mollie\Api\AbstractApiCall;
use Mollie\Client\Mollie\MollieConfig;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

class GetExpressCheckoutSessionApi extends AbstractApiCall
{
    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer|null $mollieApiRequestTransfer
     *
     * @return \Mollie\Api\Http\Request|null
     */
    protected function buildRequest(?MollieApiRequestTransfer $mollieApiRequestTransfer = null): ?Request
    {
        $this->request = new GetSessionRequest(
            $mollieApiRequestTransfer->getSessionIdOrFail(),
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
        $mollieExpressCheckoutSessionApiResponseTransfer = (new MollieExpressCheckoutSessionApiResponseTransfer())
            ->setIsSuccessful($mollieApiResponseTransfer->getIsSuccessful())
            ->setMessage($mollieApiResponseTransfer->getMessage());

        if (!$mollieApiResponseTransfer->getIsSuccessful()) {
            return $mollieExpressCheckoutSessionApiResponseTransfer;
        }

        $mollieExpressCheckoutSessionTransfer = new MollieExpressCheckoutSessionTransfer();
        $mollieExpressCheckoutSessionTransfer->fromArray($mollieApiResponseTransfer->getPayload(), true);

        $links = $mollieApiResponseTransfer->getPayload()[MollieConfig::RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS] ?? [];
        $mollieLinksTransfer = new MollieLinksTransfer();
        $mollieLinksTransfer->fromArray($links, true);
        $mollieExpressCheckoutSessionTransfer->setLinks($mollieLinksTransfer);

        $mollieExpressCheckoutSessionApiResponseTransfer->setExpressCheckoutSession($mollieExpressCheckoutSessionTransfer);

        return $mollieExpressCheckoutSessionApiResponseTransfer;
    }
}
