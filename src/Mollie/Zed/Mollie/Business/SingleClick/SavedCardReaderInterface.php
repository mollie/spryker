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
     * @param \Generated\Shared\Transfer\MollieSingleClickPaymentDataRequestTransfer $mollieSingleClickPaymentDataRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieSingleClickPaymentDataTransfer
     */
    public function getSingleClickPaymentData(
        MollieSingleClickPaymentDataRequestTransfer $mollieSingleClickPaymentDataRequestTransfer,
    ): MollieSingleClickPaymentDataTransfer;

    /**
     * @param \Generated\Shared\Transfer\MollieSingleClickPaymentDataRequestTransfer $mollieSingleClickPaymentDataRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieSingleClickPaymentDataTransfer
     */
    public function getCustomerSavedCards(
        MollieSingleClickPaymentDataRequestTransfer $mollieSingleClickPaymentDataRequestTransfer,
    ): MollieSingleClickPaymentDataTransfer;

    /**
     * @param \Generated\Shared\Transfer\MollieCustomerTransfer $mollieCustomerTransfer
     *
     * @return \ArrayObject<int, \Generated\Shared\Transfer\MollieMandateTransfer>
     */
    public function getSavedCards(MollieCustomerTransfer $mollieCustomerTransfer): ArrayObject;
}
