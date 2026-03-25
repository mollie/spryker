<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Mapper\PaymentLink;

use Generated\Shared\Transfer\MolliePaymentLinkTransfer;

interface MolliePaymentLinkMapperInterface
{
    /**
     * @param array<string, mixed> $formData
     *
     * @return \Generated\Shared\Transfer\MolliePaymentLinkTransfer
     */
    public function mapPaymentLinkFormDataToMolliePaymentLinkTransfer(array $formData): MolliePaymentLinkTransfer;
}
