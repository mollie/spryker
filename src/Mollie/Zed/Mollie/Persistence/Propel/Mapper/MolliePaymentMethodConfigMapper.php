<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer;
use Mollie\Service\Mollie\MollieServiceInterface;
use Mollie\Zed\Mollie\MollieConfig;
use Orm\Zed\Mollie\Persistence\SpyMolliePaymentMethodConfig;

class MolliePaymentMethodConfigMapper implements MolliePaymentMethodConfigMapperInterface
{
    public const string ACTIVATED = 'activated';

    public const string NOT_ACTIVATED = 'not activated';

    /**
     * @param \Mollie\Service\Mollie\MollieServiceInterface $mollieService
     * @param \Mollie\Zed\Mollie\MollieConfig $config
     */
    public function __construct(
        private MollieServiceInterface $mollieService,
        private MollieConfig $config,
    ) {
    }

    /**
     * @param array<\Orm\Zed\Mollie\Persistence\SpyMolliePaymentMethodConfig> $entities
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer
     */
    public function mapMolliePaymentMethodConfigEntitiesToCollection($entities): MolliePaymentMethodConfigCollectionTransfer
    {
        $collection = new MolliePaymentMethodConfigCollectionTransfer();
        foreach ($entities as $entity) {
            $collection->addMolliePaymentMethodConfig($this->mapMolliePaymentMethodConfigEntityToTransfer($entity));
        }

        return $collection;
    }

    /**
     * @param \Orm\Zed\Mollie\Persistence\SpyMolliePaymentMethodConfig $spyMolliePaymentMethodConfig
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer
     */
    public function mapMolliePaymentMethodConfigEntityToTransfer(SpyMolliePaymentMethodConfig $spyMolliePaymentMethodConfig): MolliePaymentMethodConfigTransfer
    {
        $maximumAmount = $this->formatAmount($spyMolliePaymentMethodConfig->getMaximumAmount(), $spyMolliePaymentMethodConfig->getCurrencyCode());
        $minimumAmount = $this->formatAmount($spyMolliePaymentMethodConfig->getMinimumAmount(), $spyMolliePaymentMethodConfig->getCurrencyCode());

        $paymentMethodConfigTransfer = new MolliePaymentMethodConfigTransfer();
        $paymentMethodConfigTransfer->fromArray($spyMolliePaymentMethodConfig->toArray(), true)
            ->setMaximumAmount($maximumAmount)
            ->setMinimumAmount($minimumAmount)
            ->setStatus($this->mapIsActiveToStatus($spyMolliePaymentMethodConfig->getIsActive()));

        return $paymentMethodConfigTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer $configTransfer
     * @param \Orm\Zed\Mollie\Persistence\SpyMolliePaymentMethodConfig $entity
     *
     * @return \Orm\Zed\Mollie\Persistence\SpyMolliePaymentMethodConfig
     */
    public function mapMolliePaymentMethodConfigTransferToEntity(
        MolliePaymentMethodConfigTransfer $configTransfer,
        SpyMolliePaymentMethodConfig $entity,
    ): SpyMolliePaymentMethodConfig {
        return $entity->fromArray($configTransfer->toArray())
            ->setMaximumAmount($this->transformAmountToInteger($configTransfer->getMaximumAmount()))
            ->setMinimumAmount($this->transformAmountToInteger($configTransfer->getMinimumAmount()));
    }

    /**
     * @param int $amount
     * @param string $currency
     *
     * @return \Generated\Shared\Transfer\MollieAmountTransfer|null
     */
    protected function formatAmount(int $amount, string $currency): ?MollieAmountTransfer
    {
        $amountTransfer = $this->mollieService->convertIntegerToMollieAmount($amount);

        return $amountTransfer->setCurrency($currency);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieAmountTransfer|null $amountTransfer
     *
     * @return int
     */
    protected function transformAmountToInteger(?MollieAmountTransfer $amountTransfer): int
    {
        return (int)round(((float)$amountTransfer->getValue()) * 100);
    }

    /**
     * @param bool $isActive
     *
     * @return string
     */
    protected function mapIsActiveToStatus(bool $isActive): string
    {
        return $isActive ? $this->config::MOLLIE_PAYMENT_METHOD_STATUS_ACTIVATED : $this->config::MOLLIE_PAYMENT_METHOD_STATUS_NOT_ACTIVATED;
    }

    /**
     * @param string $status
     *
     * @return bool
     */
    protected function mapStatusToIsActive(string $status): bool
    {
        return $status === $this->config::MOLLIE_PAYMENT_METHOD_STATUS_ACTIVATED;
    }
}
