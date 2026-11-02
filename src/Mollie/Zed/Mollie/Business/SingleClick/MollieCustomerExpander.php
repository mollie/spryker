<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\SingleClick;

use Generated\Shared\Transfer\CustomerTransfer;

class MollieCustomerExpander implements MollieCustomerExpanderInterface
{
    /**
     * @param \Mollie\Zed\Mollie\Business\SingleClick\MollieCustomerProviderInterface $mollieCustomerProvider
     */
    public function __construct(protected MollieCustomerProviderInterface $mollieCustomerProvider)
    {
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    public function expandCustomer(CustomerTransfer $customerTransfer): CustomerTransfer
    {
        $mollieCustomerTransfer = $this->mollieCustomerProvider->findMollieCustomer($customerTransfer);

        return $customerTransfer->setMollieCustomer($mollieCustomerTransfer);
    }
}
