<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieGetProfileApiResponseTransfer;
use Generated\Shared\Transfer\MollieLogApiTransfer;
use Generated\Shared\Transfer\MolliePaymentApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundApiResponseTransfer;
use Generated\Shared\Transfer\MollieRefundResponseTransfer;
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
        return $this->getFactory()->createGetCurrentProfileApi()->execute();
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
}
