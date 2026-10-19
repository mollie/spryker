<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\SingleClick;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\MollieCustomerTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface MollieCustomerProviderInterface
{
    /**
     * Specification:
     * - Returns the stored Mollie customer of the Spryker customer, or `null`.
     *
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\MollieCustomerTransfer|null
     */
    public function findMollieCustomer(CustomerTransfer $customerTransfer): ?MollieCustomerTransfer;

    /**
     * Specification:
     * - Must only be called after the shopper gave consent to save the card.
     * - Uses the stored Mollie customer of the quote's customer when one exists.
     * - Otherwise creates the customer in Mollie and stores it with the consent timestamp and consent version.
     * - Sets the Mollie customer on `QuoteTransfer.customer.mollieCustomer`; sets null when Mollie can't create it.
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    public function createMollieCustomer(QuoteTransfer $quoteTransfer): void;

    /**
     * @param \Generated\Shared\Transfer\MollieCustomerTransfer $mollieCustomerTransfer
     *
     * @return void
     */
    public function deleteMollieCustomer(MollieCustomerTransfer $mollieCustomerTransfer): void;
}
