<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Provider\Payment;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer;

interface PaymentMethodsProviderInterface 
{
    public function provide(MollieApiRequestTransfer $requestTransfer): MolliePaymentMethodsApiResponseTransfer;
}
