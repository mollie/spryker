<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Api;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

interface ApiCallInterface
{
    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer|null $mollieApiRequestTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    public function execute(?MollieApiRequestTransfer $mollieApiRequestTransfer = null): AbstractTransfer;
}
