<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer;
use Mollie\Zed\Mollie\MollieConfig;
use Orm\Zed\Mollie\Persistence\SpyMolliePaymentMethodConfig;

class MolliePaymentMethodConfigMapper implements MolliePaymentMethodConfigMapperInterface
{
    public const string ACTIVATED = 'activated';

    public const string NOT_ACTIVATED = 'not activated';

    /**
     * @param \Mollie\Zed\Mollie\MollieConfig $config
     */
    public function __construct(private MollieConfig $config)
    {
    }

    /**
     * @param array<\Orm\Zed\Mollie\Persistence\SpyMolliePaymentMethodConfig> $entities
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer
     */
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
        $paymentMethodConfigTransfer->fromArray($spyMolliePaymentMethodConfig->toArray(), true)
            ->setMaximumAmount(null)
            ->setMinimumAmount(null)
            ->setIntegerMaximumAmount($spyMolliePaymentMethodConfig->getMaximumAmount())
            ->setIntegerMinimumAmount($spyMolliePaymentMethodConfig->getMinimumAmount())
            ->setStatus($this->mapIsActiveToStatus($spyMolliePaymentMethodConfig->getIsActive()));

        return $paymentMethodConfigTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer $configTransfer
     * @param \Orm\Zed\Mollie\Persistence\SpyMolliePaymentMethodConfig $entity
     *
     * @return \Orm\Zed\Mollie\Persistence\SpyMolliePaymentMethodConfig
     */
    public function mapMolliePaymentMethodConfigTransferToEntity(
        MolliePaymentMethodConfigTransfer $configTransfer,
        SpyMolliePaymentMethodConfig $entity,
    ): SpyMolliePaymentMethodConfig {
        return $entity->fromArray($configTransfer->toArray())
            ->setMaximumAmount($configTransfer->getIntegerMaximumAmount())
            ->setMinimumAmount($configTransfer->getIntegerMinimumAmount());
    }

    /**
     * @param bool $isActive
     *
     * @return string
     */
    protected function mapIsActiveToStatus(bool $isActive): string
    {
        return $isActive ? $this->config::MOLLIE_PAYMENT_METHOD_STATUS_ACTIVATED : $this->config::MOLLIE_PAYMENT_METHOD_STATUS_NOT_ACTIVATED;
    }
}
