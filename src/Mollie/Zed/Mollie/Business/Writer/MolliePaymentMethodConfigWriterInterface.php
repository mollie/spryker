<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Writer;

use Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer;

interface MolliePaymentMethodConfigWriterInterface
{
    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer $molliePaymentMethodConfigTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer
     */
    public function writeMolliePaymentConfigData(MolliePaymentMethodConfigTransfer $molliePaymentMethodConfigTransfer): MolliePaymentMethodConfigTransfer;
}
