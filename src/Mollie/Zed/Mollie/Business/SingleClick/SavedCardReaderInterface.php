<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\SingleClick;

use ArrayObject;
use Generated\Shared\Transfer\MollieCustomerTransfer;
use Generated\Shared\Transfer\MollieSingleClickPaymentDataRequestTransfer;
use Generated\Shared\Transfer\MollieSingleClickPaymentDataTransfer;

interface SavedCardReaderInterface
{
    /**
     * Specification:
     * - Returns whether single-click payments are enabled.
     * - Returns the saved cards (valid credit card mandates) of the customer when enabled.
     *
     * @param \Generated\Shared\Transfer\MollieSingleClickPaymentDataRequestTransfer $mollieSingleClickPaymentDataRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieSingleClickPaymentDataTransfer
     */
    public function getSingleClickPaymentData(
        MollieSingleClickPaymentDataRequestTransfer $mollieSingleClickPaymentDataRequestTransfer,
    ): MollieSingleClickPaymentDataTransfer;

    /**
     * Specification:
     * - Fetches the valid credit card mandates of the Mollie customer.
     * - When Mollie no longer knows the customer (404/410), deletes the stored customer and returns no cards.
     *
     * @param \Generated\Shared\Transfer\MollieCustomerTransfer $mollieCustomerTransfer
     *
     * @return \ArrayObject<int, \Generated\Shared\Transfer\MollieMandateTransfer>
     */
    public function getSavedCards(MollieCustomerTransfer $mollieCustomerTransfer): ArrayObject;
}
