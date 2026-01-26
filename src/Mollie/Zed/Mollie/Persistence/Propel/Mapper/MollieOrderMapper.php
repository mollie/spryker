<?php

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\MolliePaymentTransfer;
use Propel\Runtime\Collection\ObjectCollection;

class MollieOrderMapper implements MollieOrderMapperInterface
{
 /**
  * @param \Propel\Runtime\Collection\ObjectCollection $spyPaymentMollieEntity
  *
  * @return \Generated\Shared\Transfer\MolliePaymentTransfer
  */
    public function mapFromSpyPaymentMollieEntityToMolliePaymentTransfer(ObjectCollection $spyPaymentMollieEntity): MolliePaymentTransfer
    {
        $spyPaymentMollieRecord = null;

        foreach ($spyPaymentMollieEntity->getData() as $spyPaymentMollie) {
            $spyPaymentMollieRecord = $spyPaymentMollie;
        }

        return (new MolliePaymentTransfer())
            ->setId($spyPaymentMollieRecord->getTransactionId())
            ->setDescription($spyPaymentMollieRecord->getDescription())
            ->setMetadata([$spyPaymentMollieRecord->getMetadata()]);
    }
}
