<?php


declare(strict_types=1);

namespace Mollie\Client\Mollie\Zed;

use Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutConfigCriteriaTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutOrderResponseTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateResponseTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentCaptureResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundResponseTransfer;
use Generated\Shared\Transfer\MollieWebhookResponseTransfer;
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
        $updateOrderCollectionResponseTransfer = $this->zedStub->call('/mollie/gateway/update-order-collection', $updateOrderCollectionRequestTransfer);

        return $updateOrderCollectionResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer $mollieExpressCheckoutPaymentUpdateRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateResponseTransfer
     */
    public function updateExpressCheckoutMolliePayment(
        MollieExpressCheckoutPaymentUpdateRequestTransfer $mollieExpressCheckoutPaymentUpdateRequestTransfer,
    ): MollieExpressCheckoutPaymentUpdateResponseTransfer {
        /** @var \Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateResponseTransfer $mollieExpressCheckoutPaymentUpdateResponseTransfer */
        $mollieExpressCheckoutPaymentUpdateResponseTransfer = $this->zedStub->call(
            '/mollie/gateway/update-express-checkout-mollie-payment',
            $mollieExpressCheckoutPaymentUpdateRequestTransfer,
        );

        return $mollieExpressCheckoutPaymentUpdateResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundResponseTransfer
     */
    public function processRefundData(MolliePaymentTransfer $molliePaymentTransfer): MollieRefundResponseTransfer
    {
        $mollieRefundResponseTransfer = $this->zedStub->call('/mollie/gateway/process-refund-data', $molliePaymentTransfer);

        return $mollieRefundResponseTransfer;
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

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutConfigCriteriaTransfer $mollieExpressCheckoutConfigCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer
     */
    public function getExpressCheckoutConfigCollection(
        MollieExpressCheckoutConfigCriteriaTransfer $mollieExpressCheckoutConfigCriteriaTransfer,
    ): MollieExpressCheckoutConfigCollectionTransfer {
        /** @var \Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer $mollieExpressCheckoutConfigCollectionTransfer */
        $mollieExpressCheckoutConfigCollectionTransfer = $this->zedStub
            ->call('/mollie/gateway/get-express-checkout-config-collection', $mollieExpressCheckoutConfigCriteriaTransfer);

        return $mollieExpressCheckoutConfigCollectionTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutOrderResponseTransfer
     */
    public function placeExpressCheckoutOrder(
        MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer,
    ): MollieExpressCheckoutOrderResponseTransfer {
        /** @var \Generated\Shared\Transfer\MollieExpressCheckoutOrderResponseTransfer $mollieExpressCheckoutOrderResponseTransfer */
        $mollieExpressCheckoutOrderResponseTransfer = $this->zedStub
            ->call('/mollie/gateway/place-express-checkout-order', $mollieExpressCheckoutOrderRequestTransfer);

        return $mollieExpressCheckoutOrderResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsRequestTransfer $mollieExpressCheckoutShippingOptionsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsResponseTransfer
     */
    public function getExpressCheckoutShippingOptions(
        MollieExpressCheckoutShippingOptionsRequestTransfer $mollieExpressCheckoutShippingOptionsRequestTransfer,
    ): MollieExpressCheckoutShippingOptionsResponseTransfer {
        /** @var \Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsResponseTransfer $mollieExpressCheckoutShippingOptionsResponseTransfer */
        $mollieExpressCheckoutShippingOptionsResponseTransfer = $this->zedStub
            ->call('/mollie/gateway/get-express-checkout-shipping-options', $mollieExpressCheckoutShippingOptionsRequestTransfer);

        return $mollieExpressCheckoutShippingOptionsResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer $mollieExpressCheckoutFailedOrderTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer
     */
    public function createExpressCheckoutFailedOrder(
        MollieExpressCheckoutFailedOrderTransfer $mollieExpressCheckoutFailedOrderTransfer,
    ): MollieExpressCheckoutFailedOrderTransfer {
        /** @var \Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer $mollieExpressCheckoutFailedOrderTransfer */
        $mollieExpressCheckoutFailedOrderTransfer = $this->zedStub
            ->call('/mollie/gateway/create-express-checkout-failed-order', $mollieExpressCheckoutFailedOrderTransfer);

        return $mollieExpressCheckoutFailedOrderTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\MollieWebhookResponseTransfer
     */
    public function processExpressCheckoutFailedOrderPayment(MolliePaymentTransfer $molliePaymentTransfer): MollieWebhookResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\MollieWebhookResponseTransfer $mollieWebhookResponseTransfer */
        $mollieWebhookResponseTransfer = $this->zedStub
            ->call('/mollie/gateway/process-express-checkout-failed-order-payment', $molliePaymentTransfer);

        return $mollieWebhookResponseTransfer;
    }
}
