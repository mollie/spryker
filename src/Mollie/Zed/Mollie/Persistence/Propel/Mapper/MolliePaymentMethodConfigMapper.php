<?php

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigTranslationTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Orm\Zed\Mollie\Persistence\SpyMolliePaymentMethodConfig;
use Orm\Zed\Mollie\Persistence\SpyMolliePaymentMethodConfigTranslation;
use Orm\Zed\Mollie\Persistence\SpyPaymentMollie;

class MolliePaymentMethodConfigMapper implements MolliePaymentMethodConfigMapperInterface
{
    public function mapMolliePaymentMethodConfigEntitiesToCollection($entities): MolliePaymentMethodConfigCollectionTransfer
    {
        $collection = new MolliePaymentMethodConfigCollectionTransfer();
        foreach ($entities as $entity) {
            $collection->addMolliePaymentMethodConfig($this->mapMolliePaymentMethodConfigEntityToTransfer($entity));
        }

        return $collection;
    }

    /**
     * @param \Orm\Zed\Mollie\Persistence\SpyMolliePaymentMethodConfig $spyMolliePaymentMethodConfig
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer
     */
    public function mapMolliePaymentMethodConfigEntityToTransfer(SpyMolliePaymentMethodConfig $spyMolliePaymentMethodConfig): MolliePaymentMethodConfigTransfer
    {
        $paymentMethodConfigTransfer = new MolliePaymentMethodConfigTransfer();
        $paymentMethodConfigTransfer->fromArray($spyMolliePaymentMethodConfig->toArray(), true);

        if (!$spyMolliePaymentMethodConfig->getSpyMolliePaymentMethodConfigTranslations()->getData()) {
            return $paymentMethodConfigTransfer;
        }

        $translations = $spyMolliePaymentMethodConfig->getSpyMolliePaymentMethodConfigTranslations()->getData();
        $paymentMethodConfigTransfer->setTranslations(
            new \ArrayObject(
                $spyMolliePaymentMethodConfig->getSpyMolliePaymentMethodConfigTranslations()
            )
        );

        return $paymentMethodConfigTransfer;
    }

    public function mapMolliePaymentMethodConfigTranslationEntityToTransfer(SpyMolliePaymentMethodConfigTranslation $configTranslation): MolliePaymentMethodConfigTranslationTransfer
    {
        $transfer = new MolliePaymentMethodConfigTranslationTransfer();
        $transfer->fromArray($configTranslation->toArray());

        return $transfer;
    }
}
