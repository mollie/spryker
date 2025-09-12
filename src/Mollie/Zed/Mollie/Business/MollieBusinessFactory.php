<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business;

use Mollie\Zed\Mollie\Business\Payment\MolliePayment;
use Mollie\Zed\Mollie\Business\Payment\MolliePaymentInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

class MollieBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Mollie\Zed\Mollie\Business\Payment\MolliePaymentInterface
     */
    public function createMolliePayment(): MolliePaymentInterface
    {
        return new MolliePayment();
    }
}
