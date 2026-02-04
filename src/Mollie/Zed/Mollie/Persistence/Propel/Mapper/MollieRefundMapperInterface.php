<?php

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\MollieRefundResponseTransfer;
use Generated\Shared\Transfer\MollieRefundSaveTransfer;
use Orm\Zed\Mollie\Persistence\SpyRefundMollie;

interface MollieRefundMapperInterface
{
    /**
     * @param \Orm\Zed\Mollie\Persistence\SpyRefundMollie $spyRefundMollieEntity
     *
     * @return \Generated\Shared\Transfer\MollieRefundResponseTransfer
     */
    public function mapFromSpyRefundMollieEntityToMollieRefundTransfer(SpyRefundMollie $spyRefundMollieEntity): MollieRefundResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\MollieRefundSaveTransfer $mollieRefundSaveTransfer
     * @param \Orm\Zed\Mollie\Persistence\SpyRefundMollie $spyRefundMollieEntity
     *
     * @return \Orm\Zed\Mollie\Persistence\SpyRefundMollie
     */
    public function mapToSpyRefundMollieEntity(
        ItemTransfer $itemTransfer,
        MollieRefundSaveTransfer $mollieRefundSaveTransfer,
        SpyRefundMollie $spyRefundMollieEntity,
    ): SpyRefundMollie;
}
