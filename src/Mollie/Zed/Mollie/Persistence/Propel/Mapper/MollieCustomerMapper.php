<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\MollieCustomerTransfer;
use Orm\Zed\Mollie\Persistence\SpyMollieCustomer;

class MollieCustomerMapper implements MollieCustomerMapperInterface
{
    /**
     * @param \Orm\Zed\Mollie\Persistence\SpyMollieCustomer $spyMollieCustomerEntity
     * @param \Generated\Shared\Transfer\MollieCustomerTransfer $mollieCustomerTransfer
     *
     * @return \Generated\Shared\Transfer\MollieCustomerTransfer
     */
    public function mapMollieCustomerEntityToTransfer(
        SpyMollieCustomer $spyMollieCustomerEntity,
        MollieCustomerTransfer $mollieCustomerTransfer,
    ): MollieCustomerTransfer {
        return $mollieCustomerTransfer->fromArray($spyMollieCustomerEntity->toArray(), true);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieCustomerTransfer $mollieCustomerTransfer
     * @param \Orm\Zed\Mollie\Persistence\SpyMollieCustomer $spyMollieCustomerEntity
     *
     * @return \Orm\Zed\Mollie\Persistence\SpyMollieCustomer
     */
    public function mapMollieCustomerTransferToEntity(
        MollieCustomerTransfer $mollieCustomerTransfer,
        SpyMollieCustomer $spyMollieCustomerEntity,
    ): SpyMollieCustomer {
        return $spyMollieCustomerEntity->fromArray($mollieCustomerTransfer->modifiedToArray());
    }
}
