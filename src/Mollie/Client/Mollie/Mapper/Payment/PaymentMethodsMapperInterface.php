<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Mapper\Payment;

use Generated\Shared\Transfer\MolliePaymentMethodCollectionTransfer;

interface PaymentMethodsMapperInterface
{
    /**
     * @param array $payload
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodCollectionTransfer
     */
    public function mapPayloadToMolliePaymentMethodCollectionTransfer(array $payload): MolliePaymentMethodCollectionTransfer;
}
