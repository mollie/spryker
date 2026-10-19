<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\SingleClick;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Spryker\Shared\Log\LoggerTrait;
use Symfony\Component\HttpFoundation\Response;

class MollieCustomerDeleter implements MollieCustomerDeleterInterface
{
    use LoggerTrait;

    /**
     * @var list<int>
     */
    protected const CUSTOMER_GONE_STATUS_CODES = [
        Response::HTTP_NOT_FOUND,
        Response::HTTP_GONE,
    ];

    /**
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     * @param \Mollie\Zed\Mollie\Business\SingleClick\MollieCustomerProviderInterface $mollieCustomerProvider
     */
    public function __construct(
        protected MollieClientInterface $mollieClient,
        protected MollieCustomerProviderInterface $mollieCustomerProvider,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return void
     */
    public function deleteMollieCustomer(CustomerTransfer $customerTransfer): void
    {
        $mollieCustomerTransfer = $this->mollieCustomerProvider->findMollieCustomer($customerTransfer);
        if (!$mollieCustomerTransfer) {
            return;
        }

        $mollieApiRequestTransfer = (new MollieApiRequestTransfer())
            ->setMollieCustomerId($mollieCustomerTransfer->getMollieCustomerId());
        $mollieCustomerApiResponseTransfer = $this->mollieClient->deleteCustomer($mollieApiRequestTransfer);

        $isCustomerGone = in_array($mollieCustomerApiResponseTransfer->getCode(), static::CUSTOMER_GONE_STATUS_CODES, true);
        if (!$mollieCustomerApiResponseTransfer->getIsSuccessful() && !$isCustomerGone) {
            $this->getLogger()->error('Mollie single-click: Mollie customer could not be deleted.', [
                'idCustomer' => $customerTransfer->getIdCustomer(),
                'mollieCustomerId' => $mollieCustomerTransfer->getMollieCustomerId(),
                'code' => $mollieCustomerApiResponseTransfer->getCode(),
                'message' => $mollieCustomerApiResponseTransfer->getMessage(),
            ]);

            return;
        }

        $this->mollieCustomerProvider->deleteMollieCustomer($mollieCustomerTransfer);
    }
}
