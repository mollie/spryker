<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\Payment\RequestSender;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface;

class MollieReleaseAuthorizationRequestSender implements MollieReleaseAuthorizationRequestSenderInterface
{
    /**
     * @param \Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface $mollieRepository
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     */
    public function __construct(
        protected MollieRepositoryInterface $mollieRepository,
        protected MollieClientInterface $mollieClient,
    ) {
    }

    /**
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function releaseAuthorization(int $idSalesOrder): void
    {
        $molliePaymentTransfer = $this->mollieRepository->getPaymentByFkSalesOrder($idSalesOrder);
        $mollieApiRequestTransfer = new MollieApiRequestTransfer();
        $mollieApiRequestTransfer->setTransactionId($molliePaymentTransfer->getId());
        $this->mollieClient->releaseAuthorization($mollieApiRequestTransfer);
    }
}
