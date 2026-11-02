<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Mapper\PaymentLink;

use ArrayObject;
use Generated\Shared\Transfer\MollieAddressTransfer;
use Generated\Shared\Transfer\OrderTransfer;

interface PaymentLinkOrderMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \ArrayObject<int, \Generated\Shared\Transfer\MollieLinesTransfer>
     */
    public function mapOrderItemsAndExpensesToMollieLines(OrderTransfer $orderTransfer): ArrayObject;

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\MollieAddressTransfer
     */
    public function mapOrderBillingAddressToMollieAddress(OrderTransfer $orderTransfer): MollieAddressTransfer;
}
