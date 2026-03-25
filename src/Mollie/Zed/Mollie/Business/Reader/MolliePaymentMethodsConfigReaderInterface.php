<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Reader;

use Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer;

interface MolliePaymentMethodsConfigReaderInterface
{
    public function getPaymentMethodConfigCollection(?int $localeId): MolliePaymentMethodConfigCollectionTransfer;

    public function getPaymentMethodConfigByMollieKey(string $key): ?MolliePaymentMethodConfigTransfer;
}

