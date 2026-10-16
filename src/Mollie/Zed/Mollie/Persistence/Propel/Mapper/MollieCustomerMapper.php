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
        return $mollieCustomerTransfer
            ->setIdMollieCustomer($spyMollieCustomerEntity->getIdMollieCustomer())
            ->setMollieCustomerId($spyMollieCustomerEntity->getMollieCustomerId())
            ->setFkCustomer($spyMollieCustomerEntity->getFkCustomer())
            ->setMode($spyMollieCustomerEntity->getMode())
            ->setConsentGivenAt($spyMollieCustomerEntity->getConsentGivenAt()?->format('Y-m-d H:i:s'))
            ->setConsentVersion($spyMollieCustomerEntity->getConsentVersion())
            ->setCreatedAt($spyMollieCustomerEntity->getCreatedAt()?->format('Y-m-d H:i:s'));
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
        return $spyMollieCustomerEntity
            ->setMollieCustomerId($mollieCustomerTransfer->getMollieCustomerIdOrFail())
            ->setFkCustomer($mollieCustomerTransfer->getFkCustomerOrFail())
            ->setMode($mollieCustomerTransfer->getModeOrFail())
            ->setConsentGivenAt($mollieCustomerTransfer->getConsentGivenAt())
            ->setConsentVersion($mollieCustomerTransfer->getConsentVersion());
    }
}
