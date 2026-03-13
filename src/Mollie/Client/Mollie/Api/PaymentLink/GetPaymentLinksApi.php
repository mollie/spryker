<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Api\PaymentLink;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\MollieLinksTransfer;
use Generated\Shared\Transfer\MolliePaymentApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\GetPaginatedPaymentLinksRequest;
use Mollie\Client\Mollie\Api\AbstractApiCall;
use Mollie\Client\Mollie\MollieConfig;
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
        $molliePaymentLinksApiResponseTransfer = (new MolliePaymentLinkApiResponseTransfer())
            ->setIsSuccessful($mollieApiResponseTransfer->getIsSuccessful())
            ->setMessage($mollieApiResponseTransfer->getMessage());

        $molliePaymentLinkCollectionTransfer = new MolliePaymentLinkCollectionTransfer();
        $payload = $mollieApiResponseTransfer->getPayload();
        $paymentLinks = $payload[MollieConfig::RESPONSE_PARAMETER_EMBEDDED][MollieConfig::RESPONSE_PARAMETER_PAYMENT_LINKS] ?? [];

        if(empty($paymentLinks)){
            return $mollieApiResponseTransfer;
        }

        foreach ($paymentLinks as $paymentLink) {
            $paymentLinkTransfer = new MolliePaymentLinkTransfer();
            $paymentLinkTransfer->fromArray($paymentLink, true);

            $links = $paymentLink[MollieConfig::RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS] ?? [];
            $mollieLinksTransfer = new MollieLinksTransfer();
            $mollieLinksTransfer->fromArray($links, true);
            $paymentLinkTransfer
                ->setLinks($mollieLinksTransfer);

            $molliePaymentLinkCollectionTransfer->addPaymentLink($paymentLinkTransfer);
        }

        $molliePaymentLinksApiResponseTransfer->setMolliePaymentLinks($molliePaymentLinkCollectionTransfer);

        return $molliePaymentLinksApiResponseTransfer;
    }
}
