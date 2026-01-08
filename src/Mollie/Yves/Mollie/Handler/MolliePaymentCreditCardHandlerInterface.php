<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Handler;

use Generated\Shared\Transfer\QuoteTransfer;

interface MolliePaymentCreditCardHandlerInterface
{
     /**
      * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
      *
      * @return \Generated\Shared\Transfer\QuoteTransfer
      */
    public function addPaymentToQuote(QuoteTransfer $quoteTransfer): QuoteTransfer;
}
