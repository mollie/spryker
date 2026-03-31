<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Api\Customer;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\MollieCustomerApiResponseTransfer;
use Generated\Shared\Transfer\MollieCustomerTransfer;
use Generated\Shared\Transfer\MollieLinksTransfer;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\CreateCustomerRequest;
use Mollie\Client\Mollie\Api\AbstractApiCall;
use Mollie\Client\Mollie\MollieConfig;
use Mollie\Shared\Mollie\MollieConstants;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Shared\Log\LoggerTrait;

class CreateCustomerApi extends AbstractApiCall
{
    use LoggerTrait;

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer|null $mollieApiRequestTransfer
     *
     * @return \Mollie\Api\Http\Request|null
     */
    protected function buildRequest(?MollieApiRequestTransfer $mollieApiRequestTransfer = null): ?Request
    {
        $quoteTransfer = $mollieApiRequestTransfer->getQuote();
        $customerTransfer = $quoteTransfer->getCustomer();

        $this->request = new CreateCustomerRequest(
            name: $customerTransfer->getFirstName() . ' ' . $customerTransfer->getLastName(),
            email: $customerTransfer->getEmail(),
            metadata: [
                MollieConstants::MOLLIE_CUSTOMER_METADATA_ID_CUSTOMER => $customerTransfer->getIdCustomer(),
            ]
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
            ->setMessage($mollieApiResponseTransfer->getMessage());

        $mollieCustomerTransfer = new MollieCustomerTransfer();
        $mollieCustomerTransfer->fromArray($mollieApiResponseTransfer->getPayload(), true);

        $links = $mollieApiResponseTransfer->getPayload()[MollieConfig::RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS] ?? [];
        $mollieLinksTransfer = new MollieLinksTransfer();
        $mollieLinksTransfer->fromArray($links, true);
        $mollieCustomerTransfer
            ->setLinks($mollieLinksTransfer);

        $mollieCustomerApiResponseTransfer->setMollieCustomer($mollieCustomerTransfer);

        return $mollieCustomerApiResponseTransfer;
    }
}
