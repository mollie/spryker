<?php


declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Controller;

use Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutConfigCriteriaTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentCaptureResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundResponseTransfer;
use Generated\Shared\Transfer\MollieSavedCardRemoveRequestTransfer;
use Generated\Shared\Transfer\MollieSavedCardRemoveResponseTransfer;
use Generated\Shared\Transfer\MollieSingleClickPaymentDataRequestTransfer;
use Generated\Shared\Transfer\MollieSingleClickPaymentDataTransfer;
use Generated\Shared\Transfer\OrderCollectionRequestTransfer;
use Generated\Shared\Transfer\OrderCollectionResponseTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * @method \Mollie\Zed\Mollie\Business\MollieFacadeInterface getFacade()
 */
class GatewayController extends AbstractGatewayController
{
    /**
     * @param \Generated\Shared\Transfer\OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\OrderCollectionResponseTransfer
     */
    public function updateOrderCollectionAction(
        OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer,
    ): OrderCollectionResponseTransfer {
        return $this->getFacade()->updateOrderCollection($updateOrderCollectionRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer $mollieExpressCheckoutPaymentUpdateRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateResponseTransfer
     */
    public function updateExpressCheckoutMolliePaymentAction(
        MollieExpressCheckoutPaymentUpdateRequestTransfer $mollieExpressCheckoutPaymentUpdateRequestTransfer,
    ): MollieExpressCheckoutPaymentUpdateResponseTransfer {
        return $this->getFacade()->updateExpressCheckoutMolliePayment($mollieExpressCheckoutPaymentUpdateRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundResponseTransfer
     */
    public function processRefundDataAction(MolliePaymentTransfer $molliePaymentTransfer): MollieRefundResponseTransfer
    {
        return $this->getFacade()->processRefundData($molliePaymentTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentCaptureResponseTransfer
     */
    public function updatePaymentCaptureCollectionAction(MolliePaymentTransfer $molliePaymentTransfer): MolliePaymentCaptureResponseTransfer
    {
        return $this->getFacade()->updatePaymentCaptureCollection($molliePaymentTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentLinkTransfer $molliePaymentLinkTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentLinkTransfer
     */
    public function updatePaymentLinkAction(MolliePaymentLinkTransfer $molliePaymentLinkTransfer): MolliePaymentLinkTransfer
    {
        return $this->getFacade()->updatePaymentLink($molliePaymentLinkTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer $molliePaymentMethodConfigCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer
     */
    public function getPaymentMethodConfigCollectionAction(
        MolliePaymentMethodConfigCriteriaTransfer $molliePaymentMethodConfigCriteriaTransfer,
    ): MolliePaymentMethodConfigCollectionTransfer {
        return $this->getFacade()->getPaymentMethodConfigCollection($molliePaymentMethodConfigCriteriaTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieSingleClickPaymentDataRequestTransfer $mollieSingleClickPaymentDataRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieSingleClickPaymentDataTransfer
     */
    public function getSingleClickPaymentDataAction(
        MollieSingleClickPaymentDataRequestTransfer $mollieSingleClickPaymentDataRequestTransfer,
    ): MollieSingleClickPaymentDataTransfer {
        return $this->getFacade()->getSingleClickPaymentData($mollieSingleClickPaymentDataRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieSingleClickPaymentDataRequestTransfer $mollieSingleClickPaymentDataRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieSingleClickPaymentDataTransfer
     */
    public function getCustomerSavedCardsAction(
        MollieSingleClickPaymentDataRequestTransfer $mollieSingleClickPaymentDataRequestTransfer,
    ): MollieSingleClickPaymentDataTransfer {
        return $this->getFacade()->getCustomerSavedCards($mollieSingleClickPaymentDataRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieSavedCardRemoveRequestTransfer $mollieSavedCardRemoveRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieSavedCardRemoveResponseTransfer
     */
    public function removeSavedCardAction(
        MollieSavedCardRemoveRequestTransfer $mollieSavedCardRemoveRequestTransfer,
    ): MollieSavedCardRemoveResponseTransfer {
        return $this->getFacade()->removeSavedCard($mollieSavedCardRemoveRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutConfigCriteriaTransfer $mollieExpressCheckoutConfigCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer
     */
    public function getExpressCheckoutConfigCollectionAction(
        MollieExpressCheckoutConfigCriteriaTransfer $mollieExpressCheckoutConfigCriteriaTransfer,
    ): MollieExpressCheckoutConfigCollectionTransfer {
        return $this->getFacade()->getExpressCheckoutConfigCollection($mollieExpressCheckoutConfigCriteriaTransfer);
    }
}
