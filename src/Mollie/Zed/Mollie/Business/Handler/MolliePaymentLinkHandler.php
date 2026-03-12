<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Handler;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Mollie\Client\Mollie\MollieClientInterface;

class MolliePaymentLinkHandler implements MolliePaymentLinkHandlerInterface
{
    /**
     * @param MollieClientInterface $mollieClient
     */
    public function __construct(protected MollieClientInterface $mollieClient)
    {
    }

    /**
     * @param MolliePaymentLinkTransfer $molliePaymentLinkTransfer
     * @return MolliePaymentLinkTransfer
     */
    public function createPaymentLink(MolliePaymentLinkTransfer $molliePaymentLinkTransfer): MolliePaymentLinkApiResponseTransfer
    {
        $mollieApiRequestTransfer = (new MollieApiRequestTransfer())
            ->setPaymentLink($molliePaymentLinkTransfer);
        
        return $this->mollieClient->createPaymentLink($mollieApiRequestTransfer);
    }

//    /**
//     * @return MolliePaymentLinkApiResponseTransfer
//     */
//    public function getPaymentLinks(): MolliePaymentLinkApiResponseTransfer
//    {
//        return $this->mollieClient->getPaymentLinks();
//    }
}