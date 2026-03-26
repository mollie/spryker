<?php

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigTranslationTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Orm\Zed\Mollie\Persistence\SpyMolliePaymentMethodConfig;
use Orm\Zed\Mollie\Persistence\SpyMolliePaymentMethodConfigTranslation;
use Orm\Zed\Mollie\Persistence\SpyPaymentMollie;

class MolliePaymentMethodConfigMapper implements MolliePaymentMethodConfigMapperInterface
{
    public const string ACTIVATED = 'activated';

    public const string NOT_ACTIVATED = 'not activated';

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
            ->setMollieId($spyMolliePaymentMethodConfig->getMollieId())
            ->setMaximumAmount($this->formatMaximumAmount($spyMolliePaymentMethodConfig->getMaximumAmount()))
            ->setMinimumAmount($this->formatMinimumAmount($spyMolliePaymentMethodConfig->getMinimumAmount()))
            ->setImage($this->formatImage($spyMolliePaymentMethodConfig->getLogoUrl()))
            ->setStatus($this->mapIsActiveToStatus($spyMolliePaymentMethodConfig->getIsActive()))
            ->setIsLogoVisible($spyMolliePaymentMethodConfig->getIsLogoVisible());


        if (!$spyMolliePaymentMethodConfig->getSpyMolliePaymentMethodConfigTranslations()->getData()) {
            return $paymentMethodConfigTransfer;
        }

        $translations = $spyMolliePaymentMethodConfig->getSpyMolliePaymentMethodConfigTranslations()->getData();
        $paymentMethodConfigTransfer->setTranslations(
            new \ArrayObject(
                $spyMolliePaymentMethodConfig->getSpyMolliePaymentMethodConfigTranslations()
            )
        );

        return $paymentMethodConfigTransfer;
    }

    public function mapMolliePaymentMethodConfigTranslationEntityToTransfer(SpyMolliePaymentMethodConfigTranslation $configTranslation): MolliePaymentMethodConfigTranslationTransfer
    {
        $transfer = new MolliePaymentMethodConfigTranslationTransfer();
        $transfer->fromArray($configTranslation->toArray());

        return $transfer;
    }

    /**
     * @param int $minimumAmount
     *
     * @return array
     */
    protected function formatMinimumAmount(int $minimumAmount): array
    {
        return [
            "value" => $minimumAmount,
            "currency" => null,
        ];
    }

    /**
     * @param int $maximumAmount
     *
     * @return array
     */
    protected function formatMaximumAmount(int $maximumAmount): array
    {
        return [
            "value" => $maximumAmount,
            "currency" => null,
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
            "size2x" => $imageUrl
        ];
    }

    /**
     * @param bool $isActiv
     * e
     * @return string
     */
    protected function mapIsActiveToStatus(bool $isActive): string
    {
        return $isActive ? static::ACTIVATED : static::NOT_ACTIVATED;
    }
}
