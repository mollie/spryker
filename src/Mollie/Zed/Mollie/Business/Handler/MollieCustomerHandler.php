<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Handler;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieCustomerApiResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;

class MollieCustomerHandler implements MollieCustomerHandlerInterface
{
    public function __construct(
        protected MollieClientInterface $mollieClient,
        protected MollieEntityManagerInterface $entityManager,
    )
    {
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @param CheckoutResponseTransfer $checkoutResponseTransfer
     * @return MollieCustomerApiResponseTransfer
     */
    public function createCustomer(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): MollieCustomerApiResponseTransfer
    {
        $mollieApiRequestTransfer = new MollieApiRequestTransfer();
        $mollieApiRequestTransfer->setQuote($quoteTransfer);

        $mollieApiResponseTranfer = $this->mollieClient->createCustomer($mollieApiRequestTransfer);

        if($mollieApiResponseTranfer->getIsSuccessful()){
            $this->entityManager->saveCustomer($mollieApiResponseTranfer->getMollieCustomer());
        }

        return $mollieApiResponseTranfer;
    }
}
