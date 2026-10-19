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
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\MollieCustomerTransfer|null
     */
    public function createMollieCustomer(QuoteTransfer $quoteTransfer): ?MollieCustomerTransfer;

    /**
     * @param \Generated\Shared\Transfer\MollieCustomerTransfer $mollieCustomerTransfer
     *
     * @return void
     */
    public function deleteMollieCustomer(MollieCustomerTransfer $mollieCustomerTransfer): void;
}
