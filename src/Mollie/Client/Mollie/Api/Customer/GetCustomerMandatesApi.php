<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Api\Customer;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\MollieMandateApiResponseTransfer;
use Generated\Shared\Transfer\MollieMandateCollectionTransfer;
use Generated\Shared\Transfer\MollieMandateTransfer;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\GetPaginatedMandateRequest;
use Mollie\Api\Types\PaymentMethod;
use Mollie\Client\Mollie\Api\AbstractApiCall;
use Mollie\Client\Mollie\MollieConfig;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

class GetCustomerMandatesApi extends AbstractApiCall
{
    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer|null $mollieApiRequestTransfer
     *
     * @return \Mollie\Api\Http\Request|null
     */
    protected function buildRequest(?MollieApiRequestTransfer $mollieApiRequestTransfer = null): ?Request
    {
        $this->request = new GetPaginatedMandateRequest(
            customerId: $mollieApiRequestTransfer->getMollieCustomerIdOrFail(),
            limit: MollieConfig::MANDATES_PAGE_LIMIT,
            scopes: [MollieConfig::MANDATE_SCOPE_CUSTOMER_PRESENT],
        );

        return $this->request;
    }

    /**
     * Specification:
     * - Keeps only valid credit card mandates, as only those can be used for single-click payments.
     *
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    protected function mapApiResponse(MollieApiResponseTransfer $mollieApiResponseTransfer): AbstractTransfer
    {
        $mollieMandateApiResponseTransfer = (new MollieMandateApiResponseTransfer())
            ->setIsSuccessful($mollieApiResponseTransfer->getIsSuccessful())
            ->setCode($mollieApiResponseTransfer->getCode())
            ->setMessage($mollieApiResponseTransfer->getMessage())
            ->setMandateCollection(new MollieMandateCollectionTransfer());

        if (!$mollieApiResponseTransfer->getIsSuccessful()) {
            return $mollieMandateApiResponseTransfer;
        }

        $payload = $mollieApiResponseTransfer->getPayload() ?? [];
        $mandates = $payload[MollieConfig::RESPONSE_PARAMETER_EMBEDDED][MollieConfig::RESPONSE_PARAMETER_MANDATES] ?? [];

        foreach ($mandates as $mandate) {
            $mollieMandateTransfer = (new MollieMandateTransfer())->fromArray($mandate, true);

            if (!$this->isUsableCardMandate($mollieMandateTransfer)) {
                continue;
            }

            $mollieMandateApiResponseTransfer->getMandateCollectionOrFail()->addMandate($mollieMandateTransfer);
        }

        return $mollieMandateApiResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieMandateTransfer $mollieMandateTransfer
     *
     * @return bool
     */
    protected function isUsableCardMandate(MollieMandateTransfer $mollieMandateTransfer): bool
    {
        return $mollieMandateTransfer->getStatus() === MollieConfig::MANDATE_STATUS_VALID
            && $mollieMandateTransfer->getMethod() === PaymentMethod::CREDITCARD;
    }
}
