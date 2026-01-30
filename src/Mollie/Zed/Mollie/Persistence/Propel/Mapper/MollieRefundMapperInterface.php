<?php

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundResponseTransfer;
use Generated\Shared\Transfer\MollieRefundTransfer;
use Orm\Zed\Mollie\Persistence\SpyRefundMollie;
use Propel\Runtime\Collection\ObjectCollection;

interface MollieRefundMapperInterface
{
    /**
     * @param \Propel\Runtime\Collection\ObjectCollection $spyRefundMollieEntity
     *
     * @return \Generated\Shared\Transfer\MollieRefundResponseTransfer
     */
    public function mapFromSpyRefundMollieEntityToMollieRefundTransfer(ObjectCollection $spyRefundMollieEntity): MollieRefundResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     * @param \Generated\Shared\Transfer\MollieRefundTransfer $mollieRefundTransfer
     * @param \Orm\Zed\Mollie\Persistence\SpyRefundMollie $spyRefundMollieEntity
     *
     * @return \Orm\Zed\Mollie\Persistence\SpyRefundMollie
     */
    public function mapToSpyRefundMollieEntity(
        ItemTransfer $itemTransfer,
        MolliePaymentTransfer $molliePaymentTransfer,
        MollieRefundTransfer $mollieRefundTransfer,
        SpyRefundMollie $spyRefundMollieEntity,
    ): SpyRefundMollie;
}
