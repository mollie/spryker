<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Api\Customer;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\MollieCustomerApiResponseTransfer;
use Generated\Shared\Transfer\MollieCustomerTransfer;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\CreateCustomerRequest;
use Mollie\Client\Mollie\Api\AbstractApiCall;
use Mollie\Shared\Mollie\MollieConstants;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

class CreateCustomerApi extends AbstractApiCall
{
    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer|null $mollieApiRequestTransfer
     *
     * @return \Mollie\Api\Http\Request|null
     */
    protected function buildRequest(?MollieApiRequestTransfer $mollieApiRequestTransfer = null): ?Request
    {
        $customerTransfer = $mollieApiRequestTransfer->getQuoteOrFail()->getCustomerOrFail();

        $this->request = new CreateCustomerRequest(
            name: trim($customerTransfer->getFirstName() . ' ' . $customerTransfer->getLastName()),
            email: $customerTransfer->getEmail(),
            metadata: [
                MollieConstants::MOLLIE_CUSTOMER_METADATA_ID_CUSTOMER => $customerTransfer->getIdCustomer(),
            ],
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
        $mollieCustomerApiResponseTransfer = (new MollieCustomerApiResponseTransfer())
            ->setIsSuccessful($mollieApiResponseTransfer->getIsSuccessful())
            ->setCode($mollieApiResponseTransfer->getCode())
            ->setMessage($mollieApiResponseTransfer->getMessage());

        if (!$mollieApiResponseTransfer->getIsSuccessful()) {
            return $mollieCustomerApiResponseTransfer;
        }

        $payload = $mollieApiResponseTransfer->getPayload() ?? [];
        $mollieCustomerTransfer = (new MollieCustomerTransfer())
            ->fromArray($payload, true)
            ->setMollieCustomerId($payload['id'] ?? null);

        return $mollieCustomerApiResponseTransfer->setMollieCustomer($mollieCustomerTransfer);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getRequestBody(): array
    {
        /** @var \Mollie\Api\Http\Requests\CreateCustomerRequest|null $createCustomerRequest */
        $createCustomerRequest = $this->request;

        if (!$createCustomerRequest) {
            return [];
        }

        return $this->maskPayload(['name', 'email'], $createCustomerRequest->payload()->all());
    }
}
