<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Mapper;

use Generated\Shared\Transfer\MolliePaymentMethodCollectionTransfer;

interface PaymentMethodMapperInterface
{
    /**
     * @param array $payload
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodCollectionTransfer
     */
    public function mapPayloadToMolliePaymentMethodCollectionTransfer(array $payload): MolliePaymentMethodCollectionTransfer;
}
