<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Orm\Zed\Mollie\Persistence\SpyMolliePaymentLink;

class MolliePaymentLinkMapper implements MolliePaymentLinkMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\MolliePaymentLinkTransfer $molliePaymentLinkTransfer
     * @param \Orm\Zed\Mollie\Persistence\SpyMolliePaymentLink $spyMolliePaymentLinkEntity
     *
     * @return \Orm\Zed\Mollie\Persistence\SpyMolliePaymentLink
     */
    public function mapMolliePaymentLinkTransferToEntity(
        MolliePaymentLinkTransfer $molliePaymentLinkTransfer,
        SpyMolliePaymentLink $spyMolliePaymentLinkEntity,
    ): SpyMolliePaymentLink {
        return $spyMolliePaymentLinkEntity->fromArray($molliePaymentLinkTransfer->toArray());
    }
}
