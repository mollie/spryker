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
     * - Returns the stored Mollie customer of the Spryker customer for the current Mollie mode, or `null`.
     *
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\MollieCustomerTransfer|null
     */
    public function findMollieCustomer(CustomerTransfer $customerTransfer): ?MollieCustomerTransfer;

    /**
     * Specification:
     * - Must only be called after the shopper gave consent to save the card.
     * - Reuses the stored Mollie customer; creates one in Mollie and stores it only when none exists (no duplicates).
     * - Stores the consent timestamp and consent version.
     * - Sets `MollieCustomerTransfer.isNew` to tell a created customer from a reused one.
     * - Returns `null` when the Mollie customer can't be created.
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\MollieCustomerTransfer|null
     */
    public function getOrCreateMollieCustomer(QuoteTransfer $quoteTransfer): ?MollieCustomerTransfer;

    /**
     * @param \Generated\Shared\Transfer\MollieCustomerTransfer $mollieCustomerTransfer
     *
     * @return void
     */
    public function deleteMollieCustomer(MollieCustomerTransfer $mollieCustomerTransfer): void;
}
