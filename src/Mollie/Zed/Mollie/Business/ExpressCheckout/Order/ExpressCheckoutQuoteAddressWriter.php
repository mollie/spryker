<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\Order;

use Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutQuoteResponseTransfer;
use Spryker\Shared\Log\LoggerTrait;
use Throwable;

class ExpressCheckoutQuoteAddressWriter implements ExpressCheckoutQuoteAddressWriterInterface
{
    use LoggerTrait;

    /**
     * @param \Mollie\Zed\Mollie\Business\ExpressCheckout\Order\ExpressCheckoutQuotePreparerInterface $quotePreparer
     */
    public function __construct(protected ExpressCheckoutQuotePreparerInterface $quotePreparer)
    {
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutQuoteResponseTransfer
     */
    public function writeAddresses(
        MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer,
    ): MollieExpressCheckoutQuoteResponseTransfer {
        try {
            $quoteTransfer = $this->quotePreparer->prepareQuote($mollieExpressCheckoutOrderRequestTransfer);
        } catch (Throwable $throwable) {
            $this->getLogger()->error('Express checkout addresses could not be saved on the quote.', ['exception' => $throwable]);

            return (new MollieExpressCheckoutQuoteResponseTransfer())
                ->setIsSuccessful(false)
                ->setErrors([$throwable->getMessage()]);
        }

        return (new MollieExpressCheckoutQuoteResponseTransfer())
            ->setIsSuccessful(true)
            ->setQuote($quoteTransfer);
    }
}
