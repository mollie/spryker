<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Dependency\Client;

use Generated\Shared\Transfer\CustomerTransfer;

interface MollieToCustomerClientInterface
{
    /**
     * @return \Generated\Shared\Transfer\CustomerTransfer|null
     */
    public function getCustomer(): ?CustomerTransfer;
}
