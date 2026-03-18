<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Handler;

use Mollie\Zed\Mollie\MollieConfig;
use Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface;

class MollieExpirationWarningHandler implements MollieExpirationWarningHandlerInterface
{
    public const int SECONDS_IN_A_DAY = 86400;

    /**
     * @param \Mollie\Zed\Mollie\MollieConfig $config
     * @param \Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface $repository
     */
    public function __construct(
        private MollieConfig $config,
        private MollieRepositoryInterface $repository,
    ) {
    }

    /**
     * @param int $orderId
     *
     * @return bool
     */
    public function shouldDisplayExpiryWarning(int $orderId): bool
    {
        $payment = $this->repository->getPaymentByFkSalesOrder($orderId);
        if (!$payment) {
            return false;
        }

        $today = strtotime(date('Y-m-d'));
        $captureBefore = strtotime($payment->getCaptureBefore());
        $secondsUntilCapture = $captureBefore - $today;
        $warningThresholdInDays = $this->config->getExpirationWarningThreshold();
        if ($secondsUntilCapture < $warningThresholdInDays * static::SECONDS_IN_A_DAY) {
            return true;
        }

        return false;
    }
}
