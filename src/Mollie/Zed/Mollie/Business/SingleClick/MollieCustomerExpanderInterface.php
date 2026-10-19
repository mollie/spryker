<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\SingleClick;

use Generated\Shared\Transfer\CustomerTransfer;

interface MollieCustomerExpanderInterface
{
    /**
     * Specification:
     * - Sets the stored Mollie customer on `CustomerTransfer.mollieCustomer`.
     * - Sets null when the customer has no stored Mollie customer.
     *
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    public function expandCustomer(CustomerTransfer $customerTransfer): CustomerTransfer;
}
