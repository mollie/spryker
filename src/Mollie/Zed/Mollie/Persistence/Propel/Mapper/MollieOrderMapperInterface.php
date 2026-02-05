<?php

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\MolliePaymentTransfer;
use Orm\Zed\Mollie\Persistence\SpyPaymentMollie;

interface MollieOrderMapperInterface
{
    /**
     * @param \Orm\Zed\Mollie\Persistence\SpyPaymentMollie $spyPaymentMollieEntity
     *
     * @return \Generated\Shared\Transfer\MolliePaymentTransfer
     */
    public function mapFromSpyPaymentMollieEntityToMolliePaymentTransfer(SpyPaymentMollie $spyPaymentMollieEntity): MolliePaymentTransfer;
}
