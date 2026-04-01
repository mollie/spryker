<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\MollieCustomerTransfer;
use Orm\Zed\Mollie\Persistence\Base\SpyMollieCustomer;

class MollieCustomerMapper implements MollieCustomerMapperInterface
{
    /**
     * @param SpyMollieCustomer $spyMollieCustomerEntity
     * @return MollieCustomerTransfer
     */
    public function mapMollieCustomerEntityToTransfer(SpyMollieCustomer $spyMollieCustomerEntity): MollieCustomerTransfer
    {
        $mollieCustomerTransfer = new MollieCustomerTransfer();
        $mollieCustomerTransfer->fromArray($spyMollieCustomerEntity->toArray(), true);

        return $mollieCustomerTransfer;
    }
}