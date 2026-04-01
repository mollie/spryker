<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\MollieCustomerTransfer;
use Orm\Zed\Mollie\Persistence\Base\SpyMollieCustomer;

interface MollieCustomerMapperInterface
{
    /**
     * @param SpyMollieCustomer $spyMollieCustomerEntity
     * @return MollieCustomerTransfer
     */
    public function mapMollieCustomerEntityToTransfer(SpyMollieCustomer $spyMollieCustomerEntity): MollieCustomerTransfer;
}