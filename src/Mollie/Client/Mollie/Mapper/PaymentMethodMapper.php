<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Mapper;

use Generated\Shared\Transfer\MolliePaymentMethodCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodTransfer;

class PaymentMethodMapper implements PaymentMethodMapperInterface
{
    protected const string METHODS_WRAPPER_KEY = '_embedded';

    protected const string METHODS_KEY = 'methods';

    /**
     * @param array $payload
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodCollectionTransfer
     */
    public function mapPayloadToMolliePaymentMethodCollectionTransfer(array $payload): MolliePaymentMethodCollectionTransfer
    {
        $molliePaymentMethodCollectionTransfer = new MolliePaymentMethodCollectionTransfer();
        $methods = $payload[static::METHODS_WRAPPER_KEY][static::METHODS_KEY] ?? [];
        foreach ($methods as $method) {
            $molliePaymentMethodTransfer = new MolliePaymentMethodTransfer();
            $molliePaymentMethodTransfer->fromArray($method, true);
            $molliePaymentMethodCollectionTransfer->addMethods($molliePaymentMethodTransfer);
        }

        return $molliePaymentMethodCollectionTransfer;
    }
}
