<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Writer;

use Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;

class MolliePaymentMethodConfigWriter implements MolliePaymentMethodConfigWriterInterface
{
    /**
     * @param \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface $entityManager
     */
    public function __construct(
        protected MollieEntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer $molliePaymentMethodConfigTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer
     */
    public function writeMolliePaymentConfigData(MolliePaymentMethodConfigTransfer $molliePaymentMethodConfigTransfer): MolliePaymentMethodConfigTransfer
    {
        if ($molliePaymentMethodConfigTransfer->getIdMolliePaymentMethodConfig()) {
            return $this->entityManager->updateMolliePaymentMethodConfig($molliePaymentMethodConfigTransfer);
        }

        return $this->entityManager->createMolliePaymentMethodConfig($molliePaymentMethodConfigTransfer);
    }
}
