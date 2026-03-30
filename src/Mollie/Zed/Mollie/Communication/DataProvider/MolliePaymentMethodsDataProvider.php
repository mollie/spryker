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
use Mollie\Shared\Mollie\MollieConstants;
use Mollie\Zed\Mollie\Business\MollieFacadeInterface;
use Mollie\Zed\Mollie\Communication\Mapper\MollieCommunicationMapperInterface;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToLocaleFacadeInterface;
use Symfony\Component\HttpFoundation\Request;

class MolliePaymentMethodsDataProvider
{
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
     *
     * @return array<string, mixed>
     */
    public function getFormData(string $mollieId): array
    {
        $data = [
            'isActive' => true,
            'isLogoVisible' => true,
            'idMolliePaymentMethodConfig' => null,
        ];

        $requestTransfer = $this->mapper->createMollieApiRequestTransfer($this->localeFacade->getCurrentLocale()->getLocaleName());
        $responseTransfer = $this->mollieClient->getAllPaymentMethods($requestTransfer);
        $molliePaymentMethodConfigTransfer = $this->mollieFacade->getPaymentMethodConfigByMollieKey($mollieId);
        $defaultValueTransfer = $this->getDefaultValueTransfer($mollieId, $responseTransfer);
        if ($defaultValueTransfer) {
            $data['mollieId'] = $defaultValueTransfer->getId();
            $data['logo'] = $defaultValueTransfer->getImage()['size2x'];
            $data['maxAmount'] = $defaultValueTransfer->getMaximumAmount()['value'];
            $data['minAmount'] = $defaultValueTransfer->getMinimumAmount()['value'];
        }

        if ($molliePaymentMethodConfigTransfer) {
            if ($molliePaymentMethodConfigTransfer->getImage()['size2x']) {
                $data['logo'] = $molliePaymentMethodConfigTransfer->getImage()['size2x'];
            }

            $data['idMolliePaymentMethodConfig'] = $molliePaymentMethodConfigTransfer->getIdMolliePaymentMethodConfig();
            $data['isActive'] = $molliePaymentMethodConfigTransfer->getIsActive();
            $data['isLogoVisible'] = $molliePaymentMethodConfigTransfer->getisLogoVisible();
            $data['mollieId'] = $molliePaymentMethodConfigTransfer->getMollieId();
        }

        return $data;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer
     */
    public function getTableData(Request $request): MolliePaymentMethodsApiResponseTransfer
    {
        $localeTransfer = $this->localeFacade->getCurrentLocale();
        $requestTransfer = $this->mapper->createMollieApiRequestTransfer($localeTransfer->getLocaleName());
        $collection = $this->mollieFacade->getPaymentMethodConfigCollection((new MolliePaymentMethodConfigCriteriaTransfer()));
        $responseTransfer = $this->getMollieDefaultValues($request, $localeTransfer->getLocaleName());

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
        $paymentMethods = $responseTransfer->getCollection()->getMethods()->getArrayCopy();
        $mappedPaymentMethods = $this->mapMolliePaymentMethodTransfersToMollieId($responseTransfer->getCollection());
        foreach ($configs as $config) {
            $paymentId = $config->getMollieId();
            if (isset($mappedPaymentMethods[$paymentId])) {
                $this->overrideSingleMethodWithConfigData($mappedPaymentMethods[$paymentId], $config);
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
     * @return void
     */
    protected function overrideSingleMethodWithConfigData(MolliePaymentMethodTransfer $methodTransfer, MolliePaymentMethodConfigTransfer $configTransfer): void
    {
        if (count($configTransfer->getMaximumAmount())) {
            $methodTransfer->setMaximumAmount(
                [
                    'value' => (string)$configTransfer->getMaximumAmount()['value'],
                    'currency' => $methodTransfer->getMaximumAmount()['currency'],
                ],
            );
        }

        if (count($configTransfer->getMinimumAmount())) {
            $methodTransfer->setMinimumAmount(
                [
                    'value' => (string)$configTransfer->getMinimumAmount()['value'],
                    'currency' => $methodTransfer->getMinimumAmount()['currency'],
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

        if ($configTransfer->getTranslations()->count()) {
            $methodTransfer->setName($configTransfer->getTranslations()->getArrayCopy()[0]->getName());
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
        $showOnlyEnabledPaymentMethods = $request->query->get(MollieConstants::MOLLIE_QUERY_PARAMETER_SHOW_ONLY_ENABLED);
        $requestTransfer = $this->mapper->createMollieApiRequestTransfer($locale);
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
}
