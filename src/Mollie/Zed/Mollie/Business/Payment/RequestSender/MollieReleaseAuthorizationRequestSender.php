<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\Payment\RequestSender;

use DateTime;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;
use Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface;

class MollieReleaseAuthorizationRequestSender implements MollieReleaseAuthorizationRequestSenderInterface
{
    /**
     * @param \Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface $mollieRepository
     * @param \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface $mollieEntityManager
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     */
    public function __construct(
        protected MollieRepositoryInterface $mollieRepository,
        protected MollieEntityManagerInterface $mollieEntityManager,
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
        $mollieApiResponseTransfer = $this->mollieClient->releaseAuthorization($mollieApiRequestTransfer);
        if ($mollieApiResponseTransfer->getIsSuccessful()) {
            $molliePaymentTransfer->setReleaseAuthorizationRequest(new DateTime());
            $this->mollieEntityManager->saveMolliePaymentReleaseAuthorizationRequest($molliePaymentTransfer);
        }
    }
}
