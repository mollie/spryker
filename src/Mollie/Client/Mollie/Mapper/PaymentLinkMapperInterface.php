<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Mapper;

use Generated\Shared\Transfer\MolliePaymentLinkTransfer;

interface PaymentLinkMapperInterface
{
    /**
     * @param array<string, mixed> $paymentLinkPayload
     *
     * @return \Generated\Shared\Transfer\MolliePaymentLinkTransfer
     */
    public function mapPayloadToMolliePaymentLinkTransfer(array $paymentLinkPayload): MolliePaymentLinkTransfer;
}
