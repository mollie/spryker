<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Writer;

use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToMoneyFacadeInterface;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;

class MolliePaymentMethodConfigWriter implements MolliePaymentMethodConfigWriterInterface
{
    /**
     * @param \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface $mollieEntityManager
     * @param \Mollie\Zed\Mollie\Dependency\Facade\MollieToMoneyFacadeInterface $moneyFacade
     */
    public function __construct(
        protected MollieEntityManagerInterface $mollieEntityManager,
        protected MollieToMoneyFacadeInterface $moneyFacade,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer $molliePaymentMethodConfigTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer
     */
    public function writePaymentMethodConfig(MolliePaymentMethodConfigTransfer $molliePaymentMethodConfigTransfer): MolliePaymentMethodConfigTransfer
    {
        $minimumMollieAmountTransfer = $molliePaymentMethodConfigTransfer->getMinimumAmount();
        $maximumMollieAmountTransfer = $molliePaymentMethodConfigTransfer->getMaximumAmount();

        $integerMinimumAmount = $this->convertMollieAmountToInteger($minimumMollieAmountTransfer);
        $integerMaximumAmount = $this->convertMollieAmountToInteger($maximumMollieAmountTransfer);

        $molliePaymentMethodConfigTransfer
            ->setIntegerMinimumAmount($integerMinimumAmount)
            ->setIntegerMaximumAmount($integerMaximumAmount);

        $savedMolliePaymentMethodConfigTransfer = $this->mollieEntityManager->writeMolliePaymentMethodConfig($molliePaymentMethodConfigTransfer);

        return $savedMolliePaymentMethodConfigTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieAmountTransfer $mollieAmountTransfer
     *
     * @return int
     */
    protected function convertMollieAmountToInteger(MollieAmountTransfer $mollieAmountTransfer): int
    {
        $decimalAmount = (float)$mollieAmountTransfer->getValue();
        $integerAmount = $this->moneyFacade->convertDecimalToInteger($decimalAmount);

        return $integerAmount;
    }
}
