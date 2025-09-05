<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business;

use Spryker\Zed\Kernel\Business\AbstractFacade;

class MollieFacade extends AbstractFacade implements MollieFacadeInterface
{
    /**
     * @return void
     */
    public function testPayment(): void
    {
        $test = 1;
    }
}
