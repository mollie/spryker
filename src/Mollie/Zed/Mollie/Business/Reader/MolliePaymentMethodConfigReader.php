<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Reader;

use Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer;
use Mollie\Service\Mollie\MollieServiceInterface;
use Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface;

class MolliePaymentMethodConfigReader implements MolliePaymentMethodConfigReaderInterface
{
    /**
     * @param \Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface $mollieRepository
     * @param \Mollie\Service\Mollie\MollieServiceInterface $mollieService
     */
    public function __construct(
        protected MollieRepositoryInterface $mollieRepository,
        protected MollieServiceInterface $mollieService,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer $criteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer
     */
    public function getPaymentMethodConfigCollection(MolliePaymentMethodConfigCriteriaTransfer $criteriaTransfer): MolliePaymentMethodConfigCollectionTransfer
    {
        $molliePaymentMethodConfigCollectionTransfer = $this->mollieRepository->getPaymentMethodConfigCollection($criteriaTransfer);

        foreach ($molliePaymentMethodConfigCollectionTransfer->getConfigs() as $molliePaymentMethodConfigTransfer) {
            $this->addMollieAmountsToPaymentMethodConfig($molliePaymentMethodConfigTransfer);
        }

        return $molliePaymentMethodConfigCollectionTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer $criteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer|null
     */
    public function getPaymentMethodConfigByCriteria(MolliePaymentMethodConfigCriteriaTransfer $criteriaTransfer): ?MolliePaymentMethodConfigTransfer
    {
        $molliePaymentMethodConfigTransfer = $this->mollieRepository->getPaymentMethodConfigByCriteria($criteriaTransfer);

        if ($molliePaymentMethodConfigTransfer === null) {
            return null;
        }

        $molliePaymentMethodConfigTransfer = $this->addMollieAmountsToPaymentMethodConfig($molliePaymentMethodConfigTransfer);

        return $molliePaymentMethodConfigTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer $molliePaymentMethodConfigTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer
     */
    protected function addMollieAmountsToPaymentMethodConfig(
        MolliePaymentMethodConfigTransfer $molliePaymentMethodConfigTransfer,
    ): MolliePaymentMethodConfigTransfer {
        $currencyCode = $molliePaymentMethodConfigTransfer->getCurrencyCode();
        $minimumAmountInCents = $molliePaymentMethodConfigTransfer->getMinimumAmountInCents();
        $maximumAmountInCents = $molliePaymentMethodConfigTransfer->getMaximumAmountInCents();

        $minimumMollieAmountTransfer = $this->mollieService->convertIntegerToMollieAmount($minimumAmountInCents, $currencyCode);
        $maximumMollieAmountTransfer = $this->mollieService->convertIntegerToMollieAmount($maximumAmountInCents, $currencyCode);

        $molliePaymentMethodConfigTransfer
            ->setMinimumAmount($minimumMollieAmountTransfer)
            ->setMaximumAmount($maximumMollieAmountTransfer);

        return $molliePaymentMethodConfigTransfer;
    }
}
