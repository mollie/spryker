<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Handler;

use DateTime;
use DateTimeZone;
use Generated\Shared\Transfer\MollieExpirationInformationTransfer;
use Mollie\Zed\Mollie\MollieConfig;
use Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface;

class MollieExpirationWarningHandler implements MollieExpirationWarningHandlerInterface
{
    public const int SECONDS_IN_A_DAY = 86400;

    public const int SECONDS_IN_AN_HOUR = 3600;

    public const array SKIPPED_STATUSES = ['closed', 'paid', 'canceled'];

    public const string CAPTURE_EXPIRING_WARNING_MESSAGE = 'mollie.order.warning.capture-expiring';

    public const string CAPTURE_EXPIRED_WARNING_MESSAGE = 'mollie.order.warning.capture-expired';

    /**
     * @param \Mollie\Zed\Mollie\MollieConfig $config
     * @param \Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface $repository
     */
    public function __construct(
        private MollieConfig $config,
        private MollieRepositoryInterface $repository,
    ) {
    }

    public function getExpirationInformation(int $orderId): MollieExpirationInformationTransfer
    {
        $transfer = $this->createExpirationInformationTransferWithDefaultData();
        $payment = $this->repository->getPaymentByFkSalesOrder($orderId);
        if (!$payment) {
            return $transfer;
        }

        if (!$payment->getCaptureBefore()) {
            return $transfer;
        }

        $paymentStatus = $payment->getStatus();
        if (in_array($paymentStatus, static::SKIPPED_STATUSES)) {
            return $transfer;
        }

        $timezone = new DateTimeZone('UTC');
        $today = new DateTime('now', $timezone);
        $captureBefore = new DateTime($payment->getCaptureBefore(), $timezone);

        $todayTimestamp = $today->getTimestamp();
        $captureBeforeTimestamp = $captureBefore->getTimestamp();

        $secondsUntilCapture = $captureBeforeTimestamp - $todayTimestamp;
        $warningThresholdInDays = $this->config->getExpirationWarningThreshold();

        if ($secondsUntilCapture < 0) {
            $transfer->setShowWarningMessage(true);
            $transfer->setWarningMessage(static::CAPTURE_EXPIRED_WARNING_MESSAGE);

            return $transfer;
        }

        if ($secondsUntilCapture <= $warningThresholdInDays * static::SECONDS_IN_A_DAY) {
            $transfer->setShowWarningMessage(true);
            $transfer->setWarningMessage(static::CAPTURE_EXPIRING_WARNING_MESSAGE);
            $transfer->setExpiresIn($this->transformSecondsIntoHours($secondsUntilCapture));
        }

        return $transfer;
    }

    /**
     * @param int $seconds
     *
     * @return int
     */
    protected function transformSecondsIntoHours(int $seconds): int
    {
        return (int)round($seconds / static::SECONDS_IN_AN_HOUR);
    }

    /**
     * @return \Generated\Shared\Transfer\MollieExpirationInformationTransfer
     */
    protected function createExpirationInformationTransferWithDefaultData(): MollieExpirationInformationTransfer
    {
        return (new MollieExpirationInformationTransfer())
            ->setShowWarningMessage(false)
            ->setWarningMessage('')
            ->setExpiresIn(0);
    }
}
