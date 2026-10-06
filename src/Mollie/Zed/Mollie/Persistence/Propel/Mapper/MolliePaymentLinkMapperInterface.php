<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Orm\Zed\Mollie\Persistence\SpyMolliePaymentLink;

interface MolliePaymentLinkMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\MolliePaymentLinkTransfer $molliePaymentLinkTransfer
     * @param \Orm\Zed\Mollie\Persistence\SpyMolliePaymentLink $spyMolliePaymentLinkEntity
     * @param int|null $amount
     * @param int|null $minimumAmount
     * @param string|null $currencyCode
     * @param string|null $paymentMethods
     *
     * @return \Orm\Zed\Mollie\Persistence\SpyMolliePaymentLink
     */
    public function mapMolliePaymentLinkTransferToEntity(
        MolliePaymentLinkTransfer $molliePaymentLinkTransfer,
        SpyMolliePaymentLink $spyMolliePaymentLinkEntity,
        ?int $amount,
        ?int $minimumAmount,
        ?string $currencyCode,
        ?string $paymentMethods,
    ): SpyMolliePaymentLink;

    /**
     * @param \Orm\Zed\Mollie\Persistence\SpyMolliePaymentLink $spyMolliePaymentLinkEntity
     *
     * @return \Generated\Shared\Transfer\MolliePaymentLinkTransfer
     */
    public function mapMolliePaymentLinkEntityToTransfer(SpyMolliePaymentLink $spyMolliePaymentLinkEntity): MolliePaymentLinkTransfer;
}
