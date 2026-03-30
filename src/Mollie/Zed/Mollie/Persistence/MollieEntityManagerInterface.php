<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence;

use Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer;
use Generated\Shared\Transfer\MolliePaymentCaptureTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundCollectionTransfer;
use Generated\Shared\Transfer\MollieRefundSaveTransfer;
use Generated\Shared\Transfer\OrderCollectionRequestTransfer;

interface MollieEntityManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer
     *
     * @return void
     */
    public function updateMolliePayment(OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer): void;

    /**
     * @param int $idSalesOrder
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return void
     */
    public function addMolliePaymentData(int $idSalesOrder, MolliePaymentTransfer $molliePaymentTransfer): void;

    /**
     * @param \Generated\Shared\Transfer\MollieRefundCollectionTransfer $mollieRefundCollectionTransfer
     *
     * @return void
     */
    public function updateMollieRefundWithStatus(MollieRefundCollectionTransfer $mollieRefundCollectionTransfer): void;

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return void
     */
    public function saveMolliePaymentReleaseAuthorizationRequest(MolliePaymentTransfer $molliePaymentTransfer): void;

    /**
     * @param \Generated\Shared\Transfer\MollieRefundSaveTransfer $mollieRefundSaveTransfer
     *
     * @return void
     */
    public function createRefund(MollieRefundSaveTransfer $mollieRefundSaveTransfer): void;

    /**
     * @param \Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer $mollieItemPaymentCaptureTransfer
     *
     * @return void
     */
    public function createCapture(MollieItemPaymentCaptureTransfer $mollieItemPaymentCaptureTransfer): void;

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentCaptureTransfer $molliePaymentCaptureTransfer
     *
     * @return void
     */
    public function updateCapture(MolliePaymentCaptureTransfer $molliePaymentCaptureTransfer): void;

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentLinkTransfer $molliePaymentLinkTransfer
     *
     * @return void
     */
    public function writePaymentLink(MolliePaymentLinkTransfer $molliePaymentLinkTransfer): void;

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentLinkTransfer $molliePaymentLinkTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentLinkTransfer
     */
    public function updatePaymentLink(MolliePaymentLinkTransfer $molliePaymentLinkTransfer): MolliePaymentLinkTransfer;
}
