<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Table\TableDataProvider;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkApiResponseTransfer;
use Mollie\Client\Mollie\MollieClientInterface;

class MolliePaymentLinkDataProvider
{
    public function __construct(
        protected MollieClientInterface $mollieClient
    )
    {
    }

    /**
     * @return MolliePaymentLinkApiResponseTransfer
     */
    public function getData(): MolliePaymentLinkApiResponseTransfer
    {
        $mollieApiRequestTransfer = new MollieApiRequestTransfer();
        $molliePaymentLinkApiResponseTransfer = $this->mollieClient->getPaymentLinks($mollieApiRequestTransfer);
        
        return $molliePaymentLinkApiResponseTransfer;
    }
}