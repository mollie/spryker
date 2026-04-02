<?php

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer;
use Mollie\Service\Mollie\MollieServiceInterface;
use Orm\Zed\Mollie\Persistence\SpyMolliePaymentMethodConfig;

class MolliePaymentMethodConfigMapper implements MolliePaymentMethodConfigMapperInterface
{
    public const string ACTIVATED = 'activated';

    public const string NOT_ACTIVATED = 'not activated';

    /**
     * @param \Mollie\Service\Mollie\MollieServiceInterface $mollieService
     */
    public function __construct(
        private MollieServiceInterface $mollieService,
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
        $paymentMethodConfigTransfer
            ->setIdMolliePaymentMethodConfig($spyMolliePaymentMethodConfig->getIdMolliePaymentMethodConfig())
            ->setMollieId($spyMolliePaymentMethodConfig->getMollieId())
            ->setCurrencyCode($spyMolliePaymentMethodConfig->getCurrencyCode())
            ->setMaximumAmount($maximumAmount)
            ->setMinimumAmount($minimumAmount)
            ->setImage($this->formatImage($spyMolliePaymentMethodConfig->getLogoUrl()))
            ->setStatus($this->mapIsActiveToStatus($spyMolliePaymentMethodConfig->getIsActive()))
            ->setIsLogoVisible($spyMolliePaymentMethodConfig->getIsLogoVisible())
            ->setIsActive($spyMolliePaymentMethodConfig->getIsActive());

        return $paymentMethodConfigTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer $configTransfer
     * @param \Orm\Zed\Mollie\Persistence\SpyMolliePaymentMethodConfig $entity
     *
     * @return \Orm\Zed\Mollie\Persistence\SpyMolliePaymentMethodConfig
     */
    public function mapMolliePaymentMethodConfigTransferToExistingEntity(
        MolliePaymentMethodConfigTransfer $configTransfer,
        SpyMolliePaymentMethodConfig $entity,
    ): SpyMolliePaymentMethodConfig {
        return $entity
            ->setIsActive($configTransfer->getIsActive())
            ->setMollieId($configTransfer->getMollieId())
            ->setCurrencyCode($configTransfer->getCurrencyCode())
            ->setMaximumAmount($this->transformAmountToInteger($configTransfer->getMaximumAmount()))
            ->setMinimumAmount($this->transformAmountToInteger($configTransfer->getMinimumAmount()))
            ->setLogoUrl($configTransfer->getimage()['size2x'] ?? null)
            ->setIsLogoVisible($configTransfer->getIsLogoVisible());
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer $configTransfer
     *
     * @return \Orm\Zed\Mollie\Persistence\SpyMolliePaymentMethodConfig
     */
    public function mapMolliePaymentMethodConfigTransferToEntity(
        MolliePaymentMethodConfigTransfer $configTransfer,
    ): SpyMolliePaymentMethodConfig {
        return (new SpyMolliePaymentMethodConfig())
            ->setIsActive($this->mapIsActiveToStatus($configTransfer->getStatus()))
            ->setMollieId($configTransfer->getMollieId())
            ->setCurrencyCode($configTransfer->getCurrencyCode())
            ->setMaximumAmount($this->transformAmountToInteger($configTransfer->getMaximumAmount()))
            ->setMinimumAmount($this->transformAmountToInteger($configTransfer->getMinimumAmount()))
            ->setLogoUrl($configTransfer->getimage()['size2x'] ?? null)
            ->setIsLogoVisible($configTransfer->getIsLogoVisible());
    }

    /**
     * @param int|null $amount
     * @param string $currency
     *
     * @return \Generated\Shared\Transfer\MollieAmountTransfer|null
     */
    protected function formatAmount(?int $amount, string $currency): ?MollieAmountTransfer
    {
        if (!$amount) {
            return null;
        }

        $amountTransfer = $this->mollieService->convertIntegerToMollieAmount($amount);

        return $amountTransfer->setCurrency($currency);
    }

    /**
     * @param string|null $imageUrl
     *
     * @return array<string, string>
     */
    protected function formatImage(?string $imageUrl): array
    {
        return [
            'size2x' => $imageUrl,
        ];
    }

    /**
     * @param \Generated\Shared\Transfer\MollieAmountTransfer|null $amountTransfer
     *
     * @return int|null
     */
    protected function transformAmountToInteger(?MollieAmountTransfer $amountTransfer): int|null
    {
        if (!$amountTransfer || $amountTransfer->getValue() === null) {
            return null;
        }

        return (int)round(((float)$amountTransfer->getValue()) * 100);
    }

    /**
     * @param bool $isActive
     *
     * @return string
     */
    protected function mapIsActiveToStatus(bool $isActive): string
    {
        return $isActive ? static::ACTIVATED : static::NOT_ACTIVATED;
    }

    /**
     * @param string $status
     *
     * @return bool
     */
    protected function mapStatusToIsActive(string $status): bool
    {
        return $status === static::ACTIVATED;
    }
}
