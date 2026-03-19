<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\Handler;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;
use Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface;

class MolliePaymentLinkHandler implements MolliePaymentLinkHandlerInterface
{
    /**
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     * @param \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface $mollieEntityManager
     * @param \Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface $mollieRepository
     */
    public function __construct(
        protected MollieClientInterface $mollieClient,
        protected MollieEntityManagerInterface $mollieEntityManager,
        protected MollieRepositoryInterface $mollieRepository,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentLinkTransfer $molliePaymentLinkTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentLinkTransfer
     */
    public function createPaymentLink(MolliePaymentLinkTransfer $molliePaymentLinkTransfer): MolliePaymentLinkApiResponseTransfer
    {
        $mollieApiRequestTransfer = (new MollieApiRequestTransfer())
            ->setPaymentLink($molliePaymentLinkTransfer);

        $molliePaymentLinkApiResponseTransfer = $this->mollieClient->createPaymentLink($mollieApiRequestTransfer);

        if ($molliePaymentLinkApiResponseTransfer->getIsSuccessful()) {
            $this->mollieEntityManager->writePaymentLink($molliePaymentLinkApiResponseTransfer->getMolliePaymentLink());
        }

        return $molliePaymentLinkApiResponseTransfer;
    }
}
