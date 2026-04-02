<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Communication\DataProvider;

use ArrayObject;
use Generated\Shared\Transfer\MolliePaymentMethodCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Zed\Mollie\Business\MollieFacadeInterface;
use Mollie\Zed\Mollie\Communication\Mapper\MollieCommunicationMapperInterface;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToLocaleFacadeInterface;
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
     */
    public function __construct(
        private MollieCommunicationMapperInterface $mapper,
        private MollieClientInterface $mollieClient,
        protected MollieToLocaleFacadeInterface $localeFacade,
        protected MollieFacadeInterface $mollieFacade,
    ) {
    }

    /**
     * @param string $mollieId
     * @param string $currencyCode
     *
     * @return array<string, mixed>
     */
    public function getFormData(string $mollieId, string $currencyCode): array
    {
        $data = [
            MolliePaymentMethodConfigTransfer::IS_ACTIVE => true,
            MolliePaymentMethodConfigTransfer::IS_LOGO_VISIBLE => true,
            MolliePaymentMethodConfigTransfer::ID_MOLLIE_PAYMENT_METHOD_CONFIG => null,
        ];

        $requestTransfer = $this->mapper->createMollieApiRequestTransfer($this->localeFacade->getCurrentLocale()->getLocaleName(), $currencyCode);
        $criteriaTransfer = $this->mapper->createMolliePaymentMethodConfigCriteriaTransfer($mollieId, $currencyCode);
        $responseTransfer = $this->mollieClient->getAllPaymentMethods($requestTransfer);
        $molliePaymentMethodConfigTransfer = $this->mollieFacade->getPaymentMethodConfigByMollieKeyAndCurrency($criteriaTransfer);
        $defaultValueTransfer = $this->getDefaultValueTransfer($mollieId, $responseTransfer);
        if ($defaultValueTransfer) {
            $data[MolliePaymentMethodConfigTransfer::MOLLIE_ID] = $defaultValueTransfer->getId();
            $data[MolliePaymentMethodConfigTransfer::IMAGE] = $defaultValueTransfer->getImage()['size2x'];
            $data[MolliePaymentMethodConfigTransfer::MAXIMUM_AMOUNT] = $defaultValueTransfer->getMaximumAmount()['value'] ?? null;
            $data[MolliePaymentMethodConfigTransfer::MINIMUM_AMOUNT] = $defaultValueTransfer->getMinimumAmount()['value'] ?? null;
            $data[MolliePaymentMethodConfigTransfer::CURRENCY_CODE] = $defaultValueTransfer->getMinimumAmount()['currency']
                ?? $defaultValueTransfer->getMaximumAmount()['currency']
                ?? null;
        }

        if ($molliePaymentMethodConfigTransfer) {
            if ($molliePaymentMethodConfigTransfer->getImage()['size2x']) {
                $data[MolliePaymentMethodConfigTransfer::IMAGE] = $molliePaymentMethodConfigTransfer->getImage()['size2x'];
            }

            $maxAmountField = $molliePaymentMethodConfigTransfer->getMaximumAmount();
            if ($maxAmountField && isset($maxAmountField['value'])) {
                $data[MolliePaymentMethodConfigTransfer::MAXIMUM_AMOUNT] = $maxAmountField['value'];
            }

            $minAmountField = $molliePaymentMethodConfigTransfer->getMinimumAmount();
            if ($minAmountField && isset($minAmountField['value'])) {
                $data[MolliePaymentMethodConfigTransfer::MINIMUM_AMOUNT] = $minAmountField['value'];
            }

            $data[MolliePaymentMethodConfigTransfer::CURRENCY_CODE] = $molliePaymentMethodConfigTransfer->getCurrencyCode();
            $data[MolliePaymentMethodConfigTransfer::ID_MOLLIE_PAYMENT_METHOD_CONFIG] = $molliePaymentMethodConfigTransfer->getIdMolliePaymentMethodConfig();
            $data[MolliePaymentMethodConfigTransfer::IS_ACTIVE] = $molliePaymentMethodConfigTransfer->getIsActive();
            $data[MolliePaymentMethodConfigTransfer::IS_LOGO_VISIBLE] = $molliePaymentMethodConfigTransfer->getisLogoVisible();
            $data[MolliePaymentMethodConfigTransfer::MOLLIE_ID] = $molliePaymentMethodConfigTransfer->getMollieId();
        }

        return $data;
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
        $currency = $request->query->get('currency');
        $mollieId = $request->query->get('mollie_payment_method_id');
        $requestTransfer = $this->mapper->createMollieApiRequestTransfer($this->localeFacade->getCurrentLocale()->getLocaleName(), $currency);
        $responseTransfer = $this->mollieClient->getAllPaymentMethods($requestTransfer);
        $defaultValueTransfer = $this->getDefaultValueTransfer($mollieId, $responseTransfer);

        $allowedValues = [
            static::VALIDATION_MAXIMUM_VALUE => $this->transformAmountToFloat($defaultValueTransfer->getMaximumAmount()),
            static::VALIDATION_MINIMUM_VALUE => $this->transformAmountToFloat($defaultValueTransfer->getMinimumAmount()),
        ];

        return array_merge($options, $allowedValues);
    }

    /**
     * @param array<string, mixed> $amount
     *
     * @return float|null
     */
    protected function transformAmountToFloat(array $amount): float|null
    {
        if (!$amount) {
            return null;
        }

        return (float)$amount['value'];
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer
     */
    public function getTableData(Request $request): MolliePaymentMethodsApiResponseTransfer
    {
        $localeTransfer = $this->localeFacade->getCurrentLocale();
        $currency = $this->extractCurrencyCode($request);
        $responseTransfer = $this->getMollieDefaultValues($request, $localeTransfer->getLocaleName());
        $collection = $this->mollieFacade->getPaymentMethodConfigCollection((new MolliePaymentMethodConfigCriteriaTransfer())->setCurrencyCode($currency));

        return $this->overrideMollieDefaultsWithConfigData($collection, $responseTransfer, $localeTransfer->getIdLocale());
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer $collection
     * @param \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer $responseTransfer
     * @param int $localeId
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer
     */
    protected function overrideMollieDefaultsWithConfigData(
        MolliePaymentMethodConfigCollectionTransfer $collection,
        MolliePaymentMethodsApiResponseTransfer $responseTransfer,
        int $localeId,
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
            (new MolliePaymentMethodCollectionTransfer())->setMethods(
                new ArrayObject(array_values($mappedPaymentMethods)),
            ),
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
            && $methodTransfer->getMaximumAmount()['currency'] === $configTransfer->getCurrencyCode()
        ) {
            return true;
        }

        if (
            $methodTransfer->getMinimumAmount()
            && $methodTransfer->getMinimumAmount()['currency'] === $configTransfer->getCurrencyCode()
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
            $methodTransfer->setMaximumAmount(
                [
                    'value' => (string)$configTransfer->getMaximumAmount()->getValue(),
                    'currency' => $configTransfer->getMaximumAmount()->getCurrency(),
                ],
            );
        }

        if (
            $configTransfer->getMinimumAmount()
            && $configTransfer->getMinimumAmount()->getValue()
        ) {
            $methodTransfer->setMinimumAmount(
                [
                    'value' => (string)$configTransfer->getMinimumAmount()->getValue(),
                    'currency' => $configTransfer->getMinimumAmount()->getCurrency(),
                ],
            );
        }

        if (
            $configTransfer->getImage()
            && isset($configTransfer->getImage()['size2x'])
            && $configTransfer->getImage()['size2x']
        ) {
            $methodTransfer->setImage($configTransfer->getImage());
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
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $locale
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer
     */
    protected function getMollieDefaultValues(Request $request, string $locale): MolliePaymentMethodsApiResponseTransfer
    {
        $currency = $this->extractCurrencyCode($request);
        $showOnlyEnabledPaymentMethods = $request->query->all()['payment_methods_filter_form']['showOnlyEnabled'] ?? false;

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
    protected function getDefaultValueTransfer(string $mollieId, MolliePaymentMethodsApiResponseTransfer $responseTransfer): ?MolliePaymentMethodTransfer
    {
        foreach ($responseTransfer->getCollection()->getMethods()->getArrayCopy() as $paymentMethodTransfer) {
            if ($paymentMethodTransfer->getId() === $mollieId) {
                return $paymentMethodTransfer;
            }
        }

        return null;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return string|null
     */
    protected function extractCurrencyCode(Request $request): string|null
    {
        $formData = $request->query->all()['payment_methods_filter_form'] ?? null;

        if (is_array($formData) && isset($formData['currency']) && $formData['currency'] !== '') {
            return (string)$formData['currency'];
        }

        return null;
    }
}
