<?php


declare(strict_types=1);

namespace Mollie\Client\Mollie;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\MollieApplePayPaymentSessionApiResponseTransfer;
use Generated\Shared\Transfer\MollieCreateCaptureApiResponseTransfer;
use Generated\Shared\Transfer\MollieCustomerApiResponseTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutConfigCriteriaTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutOrderResponseTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutSessionApiResponseTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsResponseTransfer;
use Generated\Shared\Transfer\MollieGetCaptureApiResponseTransfer;
use Generated\Shared\Transfer\MollieGetProfileApiResponseTransfer;
use Generated\Shared\Transfer\MollieLogApiTransfer;
use Generated\Shared\Transfer\MollieMandateApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundApiResponseTransfer;
use Generated\Shared\Transfer\MollieRefundResponseTransfer;
use Generated\Shared\Transfer\MollieSavedCardRemoveRequestTransfer;
use Generated\Shared\Transfer\MollieSavedCardRemoveResponseTransfer;
use Generated\Shared\Transfer\MollieSingleClickPaymentDataRequestTransfer;
use Generated\Shared\Transfer\MollieSingleClickPaymentDataTransfer;
use Generated\Shared\Transfer\MollieWebhookResponseTransfer;
use Generated\Shared\Transfer\OrderCollectionRequestTransfer;
use Generated\Shared\Transfer\OrderCollectionResponseTransfer;
use Spryker\Client\Kernel\AbstractClient;

/**
 * @method \Mollie\Client\Mollie\MollieFactory getFactory()
 */
class MollieClient extends AbstractClient implements MollieClientInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer
     */
    public function getEnabledPaymentMethods(MollieApiRequestTransfer $mollieApiRequestTransfer): MolliePaymentMethodsApiResponseTransfer
    {
        return $this->getFactory()->createPaymentMethodsProvider()->getEnabledPaymentMethods($mollieApiRequestTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer
     */
    public function getAllPaymentMethods(MollieApiRequestTransfer $mollieApiRequestTransfer): MolliePaymentMethodsApiResponseTransfer
    {
        return $this->getFactory()->createPaymentMethodsProvider()->getAllPaymentMethods($mollieApiRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentApiResponseTransfer
     */
    public function getPaymentByTransactionId(MollieApiRequestTransfer $mollieApiRequestTransfer): MolliePaymentApiResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\MolliePaymentApiResponseTransfer $molliePaymentApiResponseTransfer */
        $molliePaymentApiResponseTransfer = $this->getFactory()->createGetPaymentByTransactionIdApi()->execute($mollieApiRequestTransfer);

        return $molliePaymentApiResponseTransfer;
    }

    /**
     * Specification:
     * - Creates a payment in Mollie system
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentApiResponseTransfer
     */
    public function createPayment(MollieApiRequestTransfer $mollieApiRequestTransfer): MolliePaymentApiResponseTransfer
    {
         /** @var \Generated\Shared\Transfer\MolliePaymentApiResponseTransfer $molliePaymentApiResponseTransfer */
        $molliePaymentApiResponseTransfer = $this->getFactory()->createPaymentApi()->execute($mollieApiRequestTransfer);

        return $molliePaymentApiResponseTransfer;
    }

    /**
     * Specification:
     * - Creates a refund in Mollie system
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundApiResponseTransfer
     */
    public function createRefund(MollieApiRequestTransfer $mollieApiRequestTransfer): MollieRefundApiResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\MollieRefundApiResponseTransfer $mollieRefundApiResponseTransfer */
        $mollieRefundApiResponseTransfer = $this->getFactory()->createRefundApi()->execute($mollieApiRequestTransfer);

        return $mollieRefundApiResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundApiResponseTransfer
     */
    public function getRefundByRefundId(MollieApiRequestTransfer $mollieApiRequestTransfer): MollieRefundApiResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\MollieRefundApiResponseTransfer $mollieRefundApiResponseTransfer */
        $mollieRefundApiResponseTransfer = $this->getFactory()->createGetRefundByRefundIdApi()->execute($mollieApiRequestTransfer);

        return $mollieRefundApiResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\OrderCollectionResponseTransfer
     */
    public function updateOrderCollection(OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer): OrderCollectionResponseTransfer
    {
        return $this->getFactory()->createZedMollieStub()->updateOrderCollection($updateOrderCollectionRequestTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return void
     */
    public function updatePaymentCaptureCollection(MolliePaymentTransfer $molliePaymentTransfer): void
    {
        $this->getFactory()->createZedMollieStub()->updatePaymentCaptureCollection($molliePaymentTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MolliePaymentLinkTransfer $molliePaymentLinkTransfer
     *
     * @return void
     */
    public function updatePaymentLink(MolliePaymentLinkTransfer $molliePaymentLinkTransfer): void
    {
        $this->getFactory()->createZedMollieStub()->updatePaymentLink($molliePaymentLinkTransfer);
    }

    /**
     *  Specification:
     *  - Deletes cache for enabled payment methods API
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer $parameters
     *
     * @return void
     */
    public function deleteEnabledPaymentMethodsCache(MolliePaymentMethodQueryParametersTransfer $parameters): void
    {
        $this->getFactory()->createPaymentMethodsCacheDeleter()->deleteEnabledPaymentMethodsCache($parameters);
    }

    /**
     * Specification:
     *  - Deletes cache for all payment methods API
     *
     * @param \Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer $parameters
     *
     * @return void
     */
    public function deleteAllPaymentMethodsCache(MolliePaymentMethodQueryParametersTransfer $parameters): void
    {
        $this->getFactory()->createPaymentMethodsCacheDeleter()->deleteAllPaymentMethodsCache($parameters);
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundResponseTransfer
     */
    public function processRefundData(MolliePaymentTransfer $molliePaymentTransfer): MollieRefundResponseTransfer
    {
        return $this->getFactory()->createZedMollieStub()->processRefundData($molliePaymentTransfer);
    }

    /**
     * @return \Generated\Shared\Transfer\MollieGetProfileApiResponseTransfer
     */
    public function getCurrentProfile(): MollieGetProfileApiResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\MollieGetProfileApiResponseTransfer $mollieGetProfileApiResponseTransfer */
        $mollieGetProfileApiResponseTransfer = $this->getFactory()->createGetCurrentProfileApi()->execute();

        return $mollieGetProfileApiResponseTransfer;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieCreateCaptureApiResponseTransfer
     */
    public function createCapture(MollieApiRequestTransfer $mollieApiRequestTransfer): MollieCreateCaptureApiResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\MollieCreateCaptureApiResponseTransfer $mollieCreateCaptureApiResponseTransfer */
        $mollieCreateCaptureApiResponseTransfer = $this->getFactory()->createPaymentCaptureApi()->execute($mollieApiRequestTransfer);

        return $mollieCreateCaptureApiResponseTransfer;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieGetCaptureApiResponseTransfer
     */
    public function getCapture(MollieApiRequestTransfer $mollieApiRequestTransfer): MollieGetCaptureApiResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\MollieGetCaptureApiResponseTransfer $mollieGetCaptureApiResponseTransfer */
        $mollieGetCaptureApiResponseTransfer = $this->getFactory()->createGetPaymentCaptureApi()->execute($mollieApiRequestTransfer);

        return $mollieGetCaptureApiResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieApiResponseTransfer
     */
    public function releaseAuthorization(MollieApiRequestTransfer $mollieApiRequestTransfer): MollieApiResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer */
        $mollieApiResponseTransfer = $this->getFactory()->createReleasePaymentAuthorizationApi()->execute($mollieApiRequestTransfer);

        return $mollieApiResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieLogApiTransfer $mollieLogApiTransfer
     *
     * @return void
     */
    public function logMessage(MollieLogApiTransfer $mollieLogApiTransfer): void
    {
        $this->getFactory()->createMollieLogger()->logMessage($mollieLogApiTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentLinkApiResponseTransfer
     */
    public function createPaymentLink(MollieApiRequestTransfer $mollieApiRequestTransfer): MolliePaymentLinkApiResponseTransfer
    {
        $molliePaymentLinkApiResponseTransfer = $this->getFactory()->createPaymentLinkApi()->execute($mollieApiRequestTransfer);

        return $molliePaymentLinkApiResponseTransfer;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentLinkApiResponseTransfer
     */
    public function getPaymentLinks(MollieApiRequestTransfer $mollieApiRequestTransfer): MolliePaymentLinkApiResponseTransfer
    {
        return $this->getFactory()->createGetPaymentLinksApi()->execute($mollieApiRequestTransfer);
    }

    /**
     *  Specification:
     *
     *  - Gets payment config collection stored in DB
     *
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer $molliePaymentMethodConfigCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer
     */
    public function getPaymentMethodConfigCollection(
        MolliePaymentMethodConfigCriteriaTransfer $molliePaymentMethodConfigCriteriaTransfer,
    ): MolliePaymentMethodConfigCollectionTransfer {
        return $this->getFactory()
            ->createZedMollieStub()
            ->getPaymentMethodConfigCollection($molliePaymentMethodConfigCriteriaTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutConfigCriteriaTransfer $mollieExpressCheckoutConfigCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer
     */
    public function getExpressCheckoutConfigCollection(
        MollieExpressCheckoutConfigCriteriaTransfer $mollieExpressCheckoutConfigCriteriaTransfer,
    ): MollieExpressCheckoutConfigCollectionTransfer {
        return $this->getFactory()
            ->createZedMollieStub()
            ->getExpressCheckoutConfigCollection($mollieExpressCheckoutConfigCriteriaTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutSessionApiResponseTransfer
     */
    public function createExpressCheckoutSession(MollieApiRequestTransfer $mollieApiRequestTransfer): MollieExpressCheckoutSessionApiResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\MollieExpressCheckoutSessionApiResponseTransfer $mollieExpressCheckoutSessionApiResponseTransfer */
        $mollieExpressCheckoutSessionApiResponseTransfer = $this->getFactory()->createExpressCheckoutSessionApi()->execute($mollieApiRequestTransfer);

        return $mollieExpressCheckoutSessionApiResponseTransfer;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutSessionApiResponseTransfer
     */
    public function getExpressCheckoutSession(MollieApiRequestTransfer $mollieApiRequestTransfer): MollieExpressCheckoutSessionApiResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\MollieExpressCheckoutSessionApiResponseTransfer $mollieExpressCheckoutSessionApiResponseTransfer */
        $mollieExpressCheckoutSessionApiResponseTransfer = $this->getFactory()->createGetExpressCheckoutSessionApi()->execute($mollieApiRequestTransfer);

        return $mollieExpressCheckoutSessionApiResponseTransfer;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieApplePayPaymentSessionApiResponseTransfer
     */
    public function createApplePayPaymentSession(MollieApiRequestTransfer $mollieApiRequestTransfer): MollieApplePayPaymentSessionApiResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\MollieApplePayPaymentSessionApiResponseTransfer $mollieApplePayPaymentSessionApiResponseTransfer */
        $mollieApplePayPaymentSessionApiResponseTransfer = $this->getFactory()->createApplePayPaymentSessionApi()->execute($mollieApiRequestTransfer);

        return $mollieApplePayPaymentSessionApiResponseTransfer;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutOrderResponseTransfer
     */
    public function placeExpressCheckoutOrder(
        MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer,
    ): MollieExpressCheckoutOrderResponseTransfer {
        return $this->getFactory()
            ->createZedMollieStub()
            ->placeExpressCheckoutOrder($mollieExpressCheckoutOrderRequestTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsRequestTransfer $mollieExpressCheckoutShippingOptionsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsResponseTransfer
     */
    public function getExpressCheckoutShippingOptions(
        MollieExpressCheckoutShippingOptionsRequestTransfer $mollieExpressCheckoutShippingOptionsRequestTransfer,
    ): MollieExpressCheckoutShippingOptionsResponseTransfer {
        return $this->getFactory()
            ->createZedMollieStub()
            ->getExpressCheckoutShippingOptions($mollieExpressCheckoutShippingOptionsRequestTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer $mollieExpressCheckoutPaymentRefundTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer
     */
    public function createExpressCheckoutPaymentRefund(
        MollieExpressCheckoutPaymentRefundTransfer $mollieExpressCheckoutPaymentRefundTransfer,
    ): MollieExpressCheckoutPaymentRefundTransfer {
        return $this->getFactory()
            ->createZedMollieStub()
            ->createExpressCheckoutPaymentRefund($mollieExpressCheckoutPaymentRefundTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\MollieWebhookResponseTransfer
     */
    public function processExpressCheckoutPaymentWebhook(MolliePaymentTransfer $molliePaymentTransfer): MollieWebhookResponseTransfer
    {
        return $this->getFactory()
            ->createZedMollieStub()
            ->processExpressCheckoutPaymentWebhook($molliePaymentTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieCustomerApiResponseTransfer
     */
    public function createCustomer(MollieApiRequestTransfer $mollieApiRequestTransfer): MollieCustomerApiResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\MollieCustomerApiResponseTransfer $mollieCustomerApiResponseTransfer */
        $mollieCustomerApiResponseTransfer = $this->getFactory()
            ->createCreateCustomerApi()
            ->execute($mollieApiRequestTransfer);

        return $mollieCustomerApiResponseTransfer;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieMandateApiResponseTransfer
     */
    public function getCustomerMandates(MollieApiRequestTransfer $mollieApiRequestTransfer): MollieMandateApiResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\MollieMandateApiResponseTransfer $mollieMandateApiResponseTransfer */
        $mollieMandateApiResponseTransfer = $this->getFactory()
            ->createGetCustomerMandatesApi()
            ->execute($mollieApiRequestTransfer);

        return $mollieMandateApiResponseTransfer;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieSingleClickPaymentDataRequestTransfer $mollieSingleClickPaymentDataRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieSingleClickPaymentDataTransfer
     */
    public function getSingleClickPaymentData(
        MollieSingleClickPaymentDataRequestTransfer $mollieSingleClickPaymentDataRequestTransfer,
    ): MollieSingleClickPaymentDataTransfer {
        return $this->getFactory()
            ->createZedMollieStub()
            ->getSingleClickPaymentData($mollieSingleClickPaymentDataRequestTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieMandateApiResponseTransfer
     */
    public function revokeCustomerMandate(MollieApiRequestTransfer $mollieApiRequestTransfer): MollieMandateApiResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\MollieMandateApiResponseTransfer $mollieMandateApiResponseTransfer */
        $mollieMandateApiResponseTransfer = $this->getFactory()
            ->createRevokeCustomerMandateApi()
            ->execute($mollieApiRequestTransfer);

        return $mollieMandateApiResponseTransfer;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieCustomerApiResponseTransfer
     */
    public function deleteCustomer(MollieApiRequestTransfer $mollieApiRequestTransfer): MollieCustomerApiResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\MollieCustomerApiResponseTransfer $mollieCustomerApiResponseTransfer */
        $mollieCustomerApiResponseTransfer = $this->getFactory()
            ->createDeleteCustomerApi()
            ->execute($mollieApiRequestTransfer);

        return $mollieCustomerApiResponseTransfer;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieSingleClickPaymentDataRequestTransfer $mollieSingleClickPaymentDataRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieSingleClickPaymentDataTransfer
     */
    public function getCustomerSavedCards(
        MollieSingleClickPaymentDataRequestTransfer $mollieSingleClickPaymentDataRequestTransfer,
    ): MollieSingleClickPaymentDataTransfer {
        return $this->getFactory()
            ->createZedMollieStub()
            ->getCustomerSavedCards($mollieSingleClickPaymentDataRequestTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieSavedCardRemoveRequestTransfer $mollieSavedCardRemoveRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieSavedCardRemoveResponseTransfer
     */
    public function removeSavedCard(
        MollieSavedCardRemoveRequestTransfer $mollieSavedCardRemoveRequestTransfer,
    ): MollieSavedCardRemoveResponseTransfer {
        return $this->getFactory()
            ->createZedMollieStub()
            ->removeSavedCard($mollieSavedCardRemoveRequestTransfer);
    }
}
