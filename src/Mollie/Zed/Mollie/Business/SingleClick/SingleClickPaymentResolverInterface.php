<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\SingleClick;

use Generated\Shared\Transfer\QuoteTransfer;

interface SingleClickPaymentResolverInterface
{
    /**
     * Specification:
     * - Applies to `mollieCreditCardPayment` only; other quotes are returned unchanged.
     * - Decides the checkout path and writes it to `MollieCreditCardPayment.checkoutPath`:
     *   - `one_click`: a saved card is chosen and is still a valid mandate of the shopper's Mollie customer.
     *   - `save_card`: a new card is entered and the shopper consented to save it; the Mollie customer is reused or created.
     *   - `full`: the feature is off, the shopper is a guest, or no consent was given.
     *   - `full_fallback`: a saved card is chosen but can't be used; the card is entered on the Mollie payment page.
     * - Sets `mollieCustomerId` and `isStoreCredentials` for the payment request.
     * - Logs the path and whether the Mollie customer was created or reused.
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param string $orderReference
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function resolve(QuoteTransfer $quoteTransfer, string $orderReference): QuoteTransfer;

    /**
     * Specification:
     * - Returns `true` when the resolved checkout path is `one_click`.
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    public function isOneClickPayment(QuoteTransfer $quoteTransfer): bool;

    /**
     * Specification:
     * - Removes all saved-card data from the payment and sets the checkout path to `full_fallback`.
     * - The card is then entered on the Mollie payment page.
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param string $orderReference
     * @param string $reason
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function applyFallback(QuoteTransfer $quoteTransfer, string $orderReference, string $reason): QuoteTransfer;
}
