<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Persistence;

use Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer;
use Generated\Shared\Transfer\MolliePaymentCaptureTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundCollectionTransfer;
use Generated\Shared\Transfer\MollieRefundSaveTransfer;
use Generated\Shared\Transfer\OrderCollectionRequestTransfer;
use Orm\Zed\Mollie\Persistence\SpyMollieOrderItemPaymentCapture;
use Orm\Zed\Mollie\Persistence\SpyMolliePaymentLink;
use Orm\Zed\Mollie\Persistence\SpyMolliePaymentMethodConfig;
use Orm\Zed\Mollie\Persistence\SpyPaymentMollie;
use Orm\Zed\Mollie\Persistence\SpyRefundMollie;
use Spryker\Zed\Kernel\Persistence\AbstractEntityManager;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;

/**
 * @method \Mollie\Zed\Mollie\Persistence\MolliePersistenceFactory getFactory()
 */
class MollieEntityManager extends AbstractEntityManager implements MollieEntityManagerInterface
{
    use TransactionTrait;

    /**
     * @param \Generated\Shared\Transfer\OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer
     *
     * @return void
     */
    public function updateMolliePayment(OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer): void
    {
        $spyPaymentMolleEntity = $this->getFactory()
            ->createSpyPaymentMollieQuery()
            ->findOneByTransactionId($updateOrderCollectionRequestTransfer->getId());

        if (!$spyPaymentMolleEntity) {
            return;
        }

        $spyPaymentMolleEntity
            ->setStatus($updateOrderCollectionRequestTransfer->getStatus())
            ->setCaptureBefore($updateOrderCollectionRequestTransfer->getCaptureBefore());

        $spyPaymentMolleEntity->save();
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return void
     */
    public function saveMolliePaymentReleaseAuthorizationRequest(MolliePaymentTransfer $molliePaymentTransfer): void
    {
        $spyPaymentMolleEntity = $this->getFactory()
            ->createSpyPaymentMollieQuery()
            ->findOneByTransactionId($molliePaymentTransfer->getId());

        if (!$spyPaymentMolleEntity) {
            return;
        }

        $spyPaymentMolleEntity->setReleaseAuthorizationRequest($molliePaymentTransfer->getReleaseAuthorizationRequest());
        $spyPaymentMolleEntity->save();
    }

    /**
     * @param int $idSalesOrder
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return void
     */
    public function addMolliePaymentData(int $idSalesOrder, MolliePaymentTransfer $molliePaymentTransfer): void
    {
        $spyPaymentMollieEntity = new SpyPaymentMollie();
        $metadata = $this->getFactory()->getUtilEncodingService()->encodeJson($molliePaymentTransfer->getMetadata());
        $spyPaymentMollieEntity
            ->setFkSalesOrder($idSalesOrder)
            ->setTransactionId($molliePaymentTransfer->getId())
            ->setStatus($molliePaymentTransfer->getStatus())
            ->setIsCancelable($molliePaymentTransfer->getIsCancelable())
            ->setDescription($molliePaymentTransfer->getDescription())
            ->setSequenceType($molliePaymentTransfer->getSequenceType())
            ->setMetadata($metadata)
            ->setExpiresAt($molliePaymentTransfer->getExpiresAt())
            ->setCreatedAt($molliePaymentTransfer->getCreatedAt());

        $spyPaymentMollieEntity->save();
    }

    /**
     * @param \Generated\Shared\Transfer\MollieRefundCollectionTransfer $mollieRefundCollectionTransfer
     *
     * @return void
     */
    public function updateMollieRefundWithStatus(MollieRefundCollectionTransfer $mollieRefundCollectionTransfer): void
    {
        $this->getTransactionHandler()->handleTransaction(function () use ($mollieRefundCollectionTransfer): void {
            foreach ($mollieRefundCollectionTransfer->getRefunds() as $mollieRefundTransfer) {
                $this->getFactory()
                    ->createSpyRefundMollieQuery()
                    ->filterByRefundId($mollieRefundTransfer->getId())
                    ->update([
                        'Status' => $mollieRefundTransfer->getStatus(),
                    ]);
            }
        });
    }

    /**
     * @param \Generated\Shared\Transfer\MollieRefundSaveTransfer $mollieRefundSaveTransfer
     *
     * @return void
     */
    public function createRefund(MollieRefundSaveTransfer $mollieRefundSaveTransfer): void
    {
        $spyRefundMollieEntity = new SpyRefundMollie();

        $spyRefundMollieEntity = $this->getFactory()
            ->createMollieRefundMapper()
            ->mapToSpyRefundMollieEntity($mollieRefundSaveTransfer, $spyRefundMollieEntity);

        $spyRefundMollieEntity->save();
    }

    /**
     * @param \Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer $mollieItemPaymentCaptureTransfer
     *
     * @return void
     */
    public function createCapture(MollieItemPaymentCaptureTransfer $mollieItemPaymentCaptureTransfer): void
    {
        $spyMollieOrderItemPaymentCaptureEntity = new SpyMollieOrderItemPaymentCapture();

        $spyMollieOrderItemPaymentCaptureEntity = $this->getFactory()
            ->createMolliePaymentCaptureMapper()
            ->mapMollieOrderItemPaymentCaptureTransferToEntity($mollieItemPaymentCaptureTransfer, $spyMollieOrderItemPaymentCaptureEntity);

        $spyMollieOrderItemPaymentCaptureEntity->save();
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentCaptureTransfer $molliePaymentCaptureTransfer
     *
     * @return void
     */
    public function updateCapture(MolliePaymentCaptureTransfer $molliePaymentCaptureTransfer): void
    {
        $spyMollieOrderItemPaymentCaptureQuery = $this->getFactory()->createSpyMollieOrderItemPaymentCaptureQuery();

        $spyMollieOrderItemPaymentCaptureEntities = $spyMollieOrderItemPaymentCaptureQuery
            ->filterByCaptureId($molliePaymentCaptureTransfer->getId())
            ->filterByTransactionId($molliePaymentCaptureTransfer->getTransactionId())
            ->find();

        foreach ($spyMollieOrderItemPaymentCaptureEntities as $spyMollieOrderItemPaymentCaptureEntity) {
             $spyMollieOrderItemPaymentCaptureEntity->setStatus($molliePaymentCaptureTransfer->getStatus());
             $spyMollieOrderItemPaymentCaptureEntity->save();
        }
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentLinkTransfer $molliePaymentLinkTransfer
     *
     * @return void
     */
    public function writePaymentLink(MolliePaymentLinkTransfer $molliePaymentLinkTransfer): void
    {
        $spyMolliePaymentLink = new SpyMolliePaymentLink();

        $spyMolliePaymentLink = $this->getFactory()
            ->createMolliePaymentLinkMapper()
            ->mapMolliePaymentLinkTransferToEntity($molliePaymentLinkTransfer, $spyMolliePaymentLink);

        $spyMolliePaymentLink->save();
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentLinkTransfer $molliePaymentLinkTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentLinkTransfer
     */
    public function updatePaymentLink(MolliePaymentLinkTransfer $molliePaymentLinkTransfer): MolliePaymentLinkTransfer
    {
        $spyMolliePaymentLinkQuery = $this->getFactory()->createSpyMolliePaymentLinkQuery();

        $spyMolliePaymentLinkEntity = $spyMolliePaymentLinkQuery
            ->filterById($molliePaymentLinkTransfer->getId())
            ->findOne();

        if (!$spyMolliePaymentLinkEntity) {
            return $molliePaymentLinkTransfer;
        }

        $molliePaymentLinkTransfer->setIdMolliePaymentLink($spyMolliePaymentLinkEntity->getIdMolliePaymentLink());
        $spyMolliePaymentLinkEntity = $this->getFactory()
            ->createMolliePaymentLinkMapper()
            ->mapMolliePaymentLinkTransferToEntity($molliePaymentLinkTransfer, $spyMolliePaymentLinkEntity);

        $spyMolliePaymentLinkEntity->save();

        return $molliePaymentLinkTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer $paymentMethodConfigTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer
     */
    public function createMolliePaymentMethodConfig(MolliePaymentMethodConfigTransfer $paymentMethodConfigTransfer): MolliePaymentMethodConfigTransfer
    {
        $molliePaymentMethodConfig = new SpyMolliePaymentMethodConfig();
        $molliePaymentMethodConfig = $this->getFactory()
            ->createMolliePaymentMethodConfigMapper()
            ->mapMolliePaymentMethodConfigTransferToEntity($paymentMethodConfigTransfer);

        $molliePaymentMethodConfig->save();

        return $paymentMethodConfigTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer $paymentMethodConfigTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer
     */
    public function updateMolliePaymentMethodConfig(MolliePaymentMethodConfigTransfer $paymentMethodConfigTransfer): MolliePaymentMethodConfigTransfer
    {
        $molliePaymentMethodConfigQuery = $this->getFactory()->createSpyMolliePaymentMethodConfigQuery();
        $molliePaymentMethodConfig = $molliePaymentMethodConfigQuery
            ->filterByIdMolliePaymentMethodConfig($paymentMethodConfigTransfer->getIdMolliePaymentMethodConfig())
            ->findOne();

        if (!$molliePaymentMethodConfig) {
            return $paymentMethodConfigTransfer;
        }

        $molliePaymentMethodConfig = $this->getFactory()
            ->createMolliePaymentMethodConfigMapper()
            ->mapMolliePaymentMethodConfigTransferToExistingEntity($paymentMethodConfigTransfer, $molliePaymentMethodConfig);

        $molliePaymentMethodConfig->save();

        return $paymentMethodConfigTransfer;
    }

    /**
     * @param int $idMolliePaymentMethodConfig
     *
     * @return void
     */
    public function deleteMolliePaymentMethodConfig(int $idMolliePaymentMethodConfig): void
    {
        $molliePaymentMethodConfigQuery = $this->getFactory()->createSpyMolliePaymentMethodConfigQuery();
        $molliePaymentMethodConfig = $molliePaymentMethodConfigQuery
            ->filterByIdMolliePaymentMethodConfig($idMolliePaymentMethodConfig)
            ->findOne();

        if (!$molliePaymentMethodConfig) {
            return;
        }

        $molliePaymentMethodConfig->delete();
    }
}
