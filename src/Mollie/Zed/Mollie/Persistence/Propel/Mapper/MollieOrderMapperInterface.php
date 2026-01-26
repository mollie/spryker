<?php

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\MolliePaymentTransfer;
use Propel\Runtime\Collection\ObjectCollection;

interface MollieOrderMapperInterface
{
    /**
     * @param \Propel\Runtime\Collection\ObjectCollection $spyPaymentMollieEntity
     *
     * @return \Generated\Shared\Transfer\MolliePaymentTransfer
     */
    public function mapFromSpyPaymentMollieEntityToMolliePaymentTransfer(ObjectCollection $spyPaymentMollieEntity): MolliePaymentTransfer;
}
