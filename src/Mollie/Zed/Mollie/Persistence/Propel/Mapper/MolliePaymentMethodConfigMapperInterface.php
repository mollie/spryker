<?php

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer;
use Orm\Zed\Mollie\Persistence\SpyMolliePaymentMethodConfig;

interface MolliePaymentMethodConfigMapperInterface
{
    /**
     * @param \Orm\Zed\Mollie\Persistence\SpyMolliePaymentMethodConfig $spyMolliePaymentMethodConfig
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer
     */
    public function mapMolliePaymentMethodConfigEntityToTransfer(SpyMolliePaymentMethodConfig $spyMolliePaymentMethodConfig): MolliePaymentMethodConfigTransfer;

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer $configTransfer
     * @param \Orm\Zed\Mollie\Persistence\SpyMolliePaymentMethodConfig $entity
     *
     * @return \Orm\Zed\Mollie\Persistence\SpyMolliePaymentMethodConfig
     */
    public function mapMolliePaymentMethodConfigTransferToExistingEntity(
        MolliePaymentMethodConfigTransfer $configTransfer,
        SpyMolliePaymentMethodConfig $entity,
    ): SpyMolliePaymentMethodConfig;

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer $configTransfer
     *
     * @return \Orm\Zed\Mollie\Persistence\SpyMolliePaymentMethodConfig
     */
    public function mapMolliePaymentMethodConfigTransferToEntity(
        MolliePaymentMethodConfigTransfer $configTransfer,
    ): SpyMolliePaymentMethodConfig;

    /**
     * @param array<\Orm\Zed\Mollie\Persistence\SpyMolliePaymentMethodConfig> $entities
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer
     */
    public function mapMolliePaymentMethodConfigEntitiesToCollection($entities): MolliePaymentMethodConfigCollectionTransfer;
}
