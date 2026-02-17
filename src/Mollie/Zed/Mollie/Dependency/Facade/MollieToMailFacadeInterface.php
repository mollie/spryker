<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Dependency\Facade;

use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Spryker\Zed\Locale\Business\LocaleFacadeInterface;

interface MollieToMailFacadeInterface
{
    /**
     * @param OrderTransfer $orderTransfer
     *
     * @return void
     */
    public function handleMail(OrderTransfer $orderTransfer): void;
}
