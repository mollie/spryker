<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie\Zed;

use Generated\Shared\Transfer\MolliePaymentCaptureResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundResponseTransfer;
use Generated\Shared\Transfer\OrderCollectionRequestTransfer;
use Generated\Shared\Transfer\OrderCollectionResponseTransfer;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;

class MollieStub implements MollieStubInterface
{
    /**
     * @param \Spryker\Client\ZedRequest\ZedRequestClientInterface $zedStub
     */
    public function __construct(protected ZedRequestClientInterface $zedStub)
    {
    }

    /**
     * @param \Generated\Shared\Transfer\OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\OrderCollectionResponseTransfer
     */
    public function updateOrderCollection(OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer): OrderCollectionResponseTransfer
    {
        return $this->zedStub->call('/mollie/gateway/update-order-collection', $updateOrderCollectionRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundResponseTransfer
     */
    public function processRefundData(MolliePaymentTransfer $molliePaymentTransfer): MollieRefundResponseTransfer
    {
        return $this->zedStub->call('/mollie/gateway/process-refund-data', $molliePaymentTransfer);
    }

     /**
      * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
      *
      * @return \Generated\Shared\Transfer\MolliePaymentCaptureResponseTransfer
      */
    public function updatePaymentCaptureCollection(MolliePaymentTransfer $molliePaymentTransfer): MolliePaymentCaptureResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\MolliePaymentCaptureResponseTransfer $molliePaymentCaptureResponseTransfer */
        $molliePaymentCaptureResponseTransfer = $this->zedStub
            ->call('/mollie/gateway/update-payment-capture-collection', $molliePaymentTransfer);

        return $molliePaymentCaptureResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentLinkTransfer $molliePaymentLinkTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentLinkTransfer
     */
    public function updatePaymentLink(MolliePaymentLinkTransfer $molliePaymentLinkTransfer): MolliePaymentLinkTransfer
    {
        /** @var \Generated\Shared\Transfer\MolliePaymentLinkTransfer $molliePaymentLinkTransfer */
        $molliePaymentLinkTransfer = $this->zedStub
            ->call('/mollie/gateway/update-payment-link', $molliePaymentLinkTransfer);

        return $molliePaymentLinkTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer $molliePaymentMethodConfigCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer
     */
    public function getPaymentMethodConfigCollection(
        MolliePaymentMethodConfigCriteriaTransfer $molliePaymentMethodConfigCriteriaTransfer,
    ): MolliePaymentMethodConfigCollectionTransfer {
        /** @var \Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer $molliePaymentMethodConfigCollectionTransfer */
        $molliePaymentMethodConfigCollectionTransfer = $this->zedStub
            ->call('/mollie/gateway/get-payment-method-config-collection', $molliePaymentMethodConfigCriteriaTransfer);

        return $molliePaymentMethodConfigCollectionTransfer;
    }
}
