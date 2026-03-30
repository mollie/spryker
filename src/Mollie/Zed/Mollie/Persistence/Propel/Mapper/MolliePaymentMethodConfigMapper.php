<?php

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer;
use Orm\Zed\Mollie\Persistence\SpyMolliePaymentMethodConfig;

class MolliePaymentMethodConfigMapper implements MolliePaymentMethodConfigMapperInterface
{
    public const string ACTIVATED = 'activated';

    public const string NOT_ACTIVATED = 'not activated';

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
        $paymentMethodConfigTransfer = new MolliePaymentMethodConfigTransfer();
        $paymentMethodConfigTransfer
            ->setIdMolliePaymentMethodConfig($spyMolliePaymentMethodConfig->getIdMolliePaymentMethodConfig())
            ->setMollieId($spyMolliePaymentMethodConfig->getMollieId())
//            ->setMaximumAmount($this->formatMaximumAmount($spyMolliePaymentMethodConfig->getMaximumAmount()))
//            ->setMinimumAmount($this->formatMinimumAmount($spyMolliePaymentMethodConfig->getMinimumAmount()))
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
//            ->setMollieId($configTransfer->getMollieId())
//            ->setMaximumAmount((int)($configTransfer->getMaximumAmount()["value"]))
//            ->setMinimumAmount((int)($configTransfer->getMinimumAmount()["value"]))
            ->setLogoUrl($configTransfer->getimage()['size2x'])
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
//            ->setMaximumAmount((int)($configTransfer->getMaximumAmount()["value"]))
//            ->setMinimumAmount((int)($configTransfer->getMinimumAmount()["value"]))
            ->setLogoUrl($configTransfer->getimage()['size2x'])
            ->setIsLogoVisible($configTransfer->getIsLogoVisible());
    }

    /**
     * @param int $minimumAmount
     *
     * @return array<string, mixed>
     */
    protected function formatMinimumAmount(int $minimumAmount): array
    {
        return [
            'value' => $minimumAmount,
            'currency' => null,
        ];
    }

    /**
     * @param int $maximumAmount
     *
     * @return array<string, mixed>
     */
    protected function formatMaximumAmount(int $maximumAmount): array
    {
        return [
            'value' => $maximumAmount,
            'currency' => null,
        ];
    }

    /**
     * @param string $imageUrl
     *
     * @return array<string, string>
     */
    protected function formatImage(string $imageUrl): array
    {
        return [
            'size2x' => $imageUrl,
        ];
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
