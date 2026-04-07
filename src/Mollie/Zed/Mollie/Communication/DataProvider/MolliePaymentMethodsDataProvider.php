<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Communication\DataProvider;

use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Shared\Mollie\MollieConstants;
use Mollie\Zed\Mollie\Business\MollieFacadeInterface;
use Mollie\Zed\Mollie\Communication\Mapper\MollieCommunicationMapperInterface;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToLocaleFacadeInterface;
use Mollie\Zed\Mollie\MollieConfig;
use Symfony\Component\HttpFoundation\Request;

class MolliePaymentMethodsDataProvider
{
    public const string VALIDATION_MINIMUM_VALUE = 'VALIDATION_MINIMUM_VALUE';

    public const string VALIDATION_MAXIMUM_VALUE = 'VALIDATION_MAXIMUM_VALUE';

    /**
     * @param \Mollie\Zed\Mollie\Communication\Mapper\MollieCommunicationMapperInterface $mapper
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     * @param \Mollie\Zed\Mollie\Dependency\Facade\MollieToLocaleFacadeInterface $localeFacade
     * @param \Mollie\Zed\Mollie\Business\MollieFacadeInterface $mollieFacade
     * @param \Mollie\Zed\Mollie\MollieConfig $config
     */
    public function __construct(
        private MollieCommunicationMapperInterface $mapper,
        private MollieClientInterface $mollieClient,
        protected MollieToLocaleFacadeInterface $localeFacade,
        protected MollieFacadeInterface $mollieFacade,
        protected MollieConfig $config,
    ) {
    }

    /**
     * @param string $mollieId
     * @param string $currencyCode
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer
     */
    public function getFormData(string $mollieId, string $currencyCode): MolliePaymentMethodConfigTransfer
    {
        $criteriaTransfer = $this->mapper->createMolliePaymentMethodConfigCriteriaTransfer($mollieId, $currencyCode);
        $molliePaymentMethodConfigTransfer = $this->mollieFacade->getPaymentMethodConfigByCriteria($criteriaTransfer);

        if ($molliePaymentMethodConfigTransfer) {
            return $molliePaymentMethodConfigTransfer;
        }

        $requestTransfer = $this->mapper->createMollieApiRequestTransfer($this->localeFacade->getCurrentLocale()->getLocaleName(), $currencyCode);
        $responseTransfer = $this->mollieClient->getAllPaymentMethods($requestTransfer);
        $defaultValueTransfer = $this->extractPaymentMethodsByPaymentKeyFromMollieResponse($mollieId, $responseTransfer);

        $paymentMethodConfigTransfer = (new MolliePaymentMethodConfigTransfer())
            ->setIsLogoVisible(true);

        if ($defaultValueTransfer) {
            $paymentMethodConfigTransfer->setMollieId($defaultValueTransfer->getId())
                ->setIsActive($defaultValueTransfer->getStatus() === $this->config::MOLLIE_PAYMENT_METHOD_STATUS_ACTIVATED)
                ->setImage($defaultValueTransfer->getImage()['size2x'])
                ->setMaximumAmount($defaultValueTransfer->getMaximumAmount())
                ->setMinimumAmount($defaultValueTransfer->getMinimumAmount())
                ->setCurrencyCode($defaultValueTransfer->getMinimumAmount()['currency']
                    ?? $defaultValueTransfer->getMaximumAmount()['currency']
                    ?? null);
        }

        return $paymentMethodConfigTransfer;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array<string, mixed>
     */
    public function getFormOptions(Request $request): array
    {
        $options = [];
        $options = $this->expandOptionsWithAllowedValues($request, $options);

        return $options;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    protected function expandOptionsWithAllowedValues(Request $request, array $options): array
    {
        $currency = $request->query->get(MollieConstants::QUERY_CURRENCY);
        $mollieId = $request->query->get(MollieConstants::QUERY_MOLLIE_PAYMENT_METHOD_ID);
        $requestTransfer = $this->mapper->createMollieApiRequestTransfer($this->localeFacade->getCurrentLocale()->getLocaleName(), $currency);
        $responseTransfer = $this->mollieClient->getAllPaymentMethods($requestTransfer);
        $defaultValueTransfer = $this->extractPaymentMethodsByPaymentKeyFromMollieResponse($mollieId, $responseTransfer);

        $allowedValues = [
            static::VALIDATION_MAXIMUM_VALUE => $this->transformAmountToFloat($defaultValueTransfer->getMaximumAmount()),
            static::VALIDATION_MINIMUM_VALUE => $this->transformAmountToFloat($defaultValueTransfer->getMinimumAmount()),
        ];

        return array_merge($options, $allowedValues);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieAmountTransfer $amountTransfer
     *
     * @return float|null
     */
    protected function transformAmountToFloat(MollieAmountTransfer $amountTransfer): float|null
    {
        if (!$amountTransfer->getValue()) {
            return null;
        }

        return (float)$amountTransfer['value'];
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer $criteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer
     */
    public function getTableData(MolliePaymentMethodConfigCriteriaTransfer $criteriaTransfer): MolliePaymentMethodsApiResponseTransfer
    {
        $localeTransfer = $this->localeFacade->getCurrentLocale();
        $currency = $criteriaTransfer->getCurrencyCode();
        $responseTransfer = $this->getMollieDefaultValues($criteriaTransfer, $localeTransfer->getLocaleName());
        $criteriaTransfer = $this->mapper->createMolliePaymentMethodConfigCriteriaTransfer(null, $currency);
        $collection = $this->mollieFacade->getPaymentMethodConfigCollection($criteriaTransfer);

        return $this->overrideMollieDefaultsWithConfigData($collection, $responseTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer $collection
     * @param \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer $responseTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer
     */
    protected function overrideMollieDefaultsWithConfigData(
        MolliePaymentMethodConfigCollectionTransfer $collection,
        MolliePaymentMethodsApiResponseTransfer $responseTransfer,
    ): MolliePaymentMethodsApiResponseTransfer {
        $configs = $collection->getConfigs()->getArrayCopy();
        $mappedPaymentMethods = $this->mapMolliePaymentMethodTransfersToMollieId($responseTransfer->getCollection());
        foreach ($configs as $config) {
            $paymentId = $config->getMollieId();
            if (isset($mappedPaymentMethods[$paymentId])) {
                if ($this->isSameCurrencyUsed($mappedPaymentMethods[$paymentId], $config)) {
                    $this->overrideSingleMethodWithConfigData($mappedPaymentMethods[$paymentId], $config);
                }
            }
        }

        return $responseTransfer->setCollection(
            $this->mapper->createMolliePaymentMethodCollection(array_values($mappedPaymentMethods)),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodTransfer $methodTransfer
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer $configTransfer
     *
     * @return bool
     */
    protected function isSameCurrencyUsed(MolliePaymentMethodTransfer $methodTransfer, MolliePaymentMethodConfigTransfer $configTransfer): bool
    {
        if (
            $methodTransfer->getMaximumAmount()
            && $methodTransfer->getMaximumAmount()->getCurrency() === $configTransfer->getCurrencyCode()
        ) {
            return true;
        }

        if (
            $methodTransfer->getMinimumAmount()
            && $methodTransfer->getMinimumAmount()->getCurrency() === $configTransfer->getCurrencyCode()
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodTransfer $methodTransfer
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer $configTransfer
     *
     * @return void
     */
    protected function overrideSingleMethodWithConfigData(MolliePaymentMethodTransfer $methodTransfer, MolliePaymentMethodConfigTransfer $configTransfer): void
    {
        if (
            $configTransfer->getMaximumAmount()
            && $configTransfer->getMaximumAmount()->getValue()
        ) {
            $methodTransfer->setMaximumAmount($configTransfer->getMaximumAmount());
        }

        if (
            $configTransfer->getMinimumAmount()
            && $configTransfer->getMinimumAmount()->getValue()
        ) {
            $methodTransfer->setMinimumAmount($configTransfer->getMinimumAmount());
        }

        if ($configTransfer->getImage()) {
            $methodTransfer->setImage(['size2x' => $configTransfer->getImage()]);
        }

        if ($configTransfer->getIsActive() !== null) {
            $methodTransfer->setStatus($configTransfer->getStatus());
        }
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodCollectionTransfer $collection
     *
     * @return array<\Generated\Shared\Transfer\MolliePaymentMethodTransfer>
     */
    protected function mapMolliePaymentMethodTransfersToMollieId(MolliePaymentMethodCollectionTransfer $collection): array
    {
        $mapped = [];
        foreach ($collection->getMethods() as $paymentMethod) {
            $mapped[$paymentMethod->getId()] = $paymentMethod;
        }

        return $mapped;
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer $criteriaTransfer
     * @param string $locale
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer
     */
    protected function getMollieDefaultValues(
        MolliePaymentMethodConfigCriteriaTransfer $criteriaTransfer,
        string $locale,
    ): MolliePaymentMethodsApiResponseTransfer {
        $currency = $criteriaTransfer->getCurrencyCode();
        $showOnlyEnabledPaymentMethods = $criteriaTransfer->getShowOnlyEnabled();

        $requestTransfer = $this->mapper->createMollieApiRequestTransfer($locale, $currency);
        if ($showOnlyEnabledPaymentMethods) {
            return $this->mollieClient->getEnabledPaymentMethods($requestTransfer);
        }

        return $this->mollieClient->getAllPaymentMethods($requestTransfer);
    }

    /**
     * @param string $mollieId
     * @param \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer $responseTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodTransfer|null
     */
    protected function extractPaymentMethodsByPaymentKeyFromMollieResponse(
        string $mollieId,
        MolliePaymentMethodsApiResponseTransfer $responseTransfer,
    ): ?MolliePaymentMethodTransfer {
        foreach ($responseTransfer->getCollection()->getMethods()->getArrayCopy() as $paymentMethodTransfer) {
            if ($paymentMethodTransfer->getId() === $mollieId) {
                return $paymentMethodTransfer;
            }
        }

        return null;
    }
}
