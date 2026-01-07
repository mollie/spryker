<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence;

use Generated\Shared\Transfer\OrderCollectionRequestTransfer;
use Spryker\Zed\Kernel\Persistence\AbstractEntityManager;

/**
 * @method \Mollie\Zed\Mollie\Persistence\MolliePersistenceFactory getFactory()
 */
class MollieEntityManager extends AbstractEntityManager implements MollieEntityManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer
     *
     * @return void
     */
    public function updateMolliePaymentWithStatus(OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer): void
    {
        $spyPaymentMolleEntity = $this->getFactory()
            ->createSpyPaymentMollieQuery()
            ->findOneByTransactionId($updateOrderCollectionRequestTransfer->getId());

        if (!$spyPaymentMolleEntity) {
            return;
        }

        $spyPaymentMolleEntity->setStatus($updateOrderCollectionRequestTransfer->getStatus());
        $spyPaymentMolleEntity->save();
    }
}
