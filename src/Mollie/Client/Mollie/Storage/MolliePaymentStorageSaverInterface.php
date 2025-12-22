<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Storage;

interface MolliePaymentStorageSaverInterface
{
    /**
     * @param string $orderReference
     * @param string $paymentId
     *
     * @throws \Exception
     *
     * @return void
     */
    public function savePaymentIdKey(string $orderReference, string $paymentId): void;
}
