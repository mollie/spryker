<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Communication\Table\TableDataProvider;

use Generated\Shared\Transfer\MolliePaymentMethodCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer;
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
     */
    public function __construct(
        private MollieCommunicationMapperInterface $mapper,
        private MollieClientInterface $mollieClient,
        protected MollieToLocaleFacadeInterface $localeFacade,
        protected MollieFacadeInterface $mollieFacade,
    ) {
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer
     */
    public function getData(Request $request): MolliePaymentMethodsApiResponseTransfer
    {
        $localeTransfer = $this->localeFacade->getCurrentLocale();
        $requestTransfer = $this->mapper->createMollieApiRequestTransfer($localeTransfer->getLocaleName());
        $collection = $this->mollieFacade->getPaymentMethodConfigCollection($localeTransfer->getIdLocale());
        $responseTransfer = $this->getMollieDefaultValues($request, $localeTransfer->getLocaleName());

        return $this->overrideMollieDefaultsWithConfigData($collection, $responseTransfer, $localeTransfer->getIdLocale());
    }

    protected function overrideMollieDefaultsWithConfigData(
        MolliePaymentMethodConfigCollectionTransfer $collection,
        MolliePaymentMethodsApiResponseTransfer $responseTransfer,
        int $localeId
    ): MolliePaymentMethodsApiResponseTransfer {

        $configs = $collection->getConfigs()->getArrayCopy();
        $paymentMethods = $responseTransfer->getCollection()->getMethods()->getArrayCopy();
        $mappedPaymentMethods = $this->mapMolliePaymentMethodTransfersToMollieId($responseTransfer->getCollection());
        foreach ($configs as $config) {
            $paymentId = $config->getPaymentMethodKey();
            if (isset($mappedPaymentMethods[$paymentId])) {
               $this->do($mappedPaymentMethods[$paymentId], $config);
            }
        }

        return $responseTransfer->setCollection(
            (new MolliePaymentMethodCollectionTransfer())->setMethods(
                new \ArrayObject(array_values($mappedPaymentMethods))
            )
        );
    }

    protected function do(MolliePaymentMethodTransfer $methodTransfer, MolliePaymentMethodConfigTransfer $configTransfer): void
    {
        if ($configTransfer->getMaxAmount() !== null) {
            $methodTransfer->setMaximumAmount(
                [
                    "value" => (string)$configTransfer->getMaxAmount(),
                    "currency" => $methodTransfer->getMaximumAmount()["currency"]
                ]
            );
        }

        if ($configTransfer->getMinAmount() !== null) {
            $methodTransfer->setMinimumAmount(
                [
                    "value" => (string)$configTransfer->getMinAmount(),
                    "currency" => $methodTransfer->getMinimumAmount()["currency"]
                ]
            );
        }

        if ($configTransfer->getLogoUrl() !== null) {
            $methodTransfer->setImage(
                [
//                    "size1x" => $configTransfer->getLogoUrl(),
                    "size2x" => $configTransfer->getLogoUrl(),
//                    "svg" => $configTransfer->getLogoUrl()
                ]
            );
        }

        if ($configTransfer->getTranslations()->count()) {
            $methodTransfer->setName($configTransfer->getTranslations()->getArrayCopy()[0]->getName());
        }

//        if ($configTransfer->getIsActive() !== null) {
//            $methodTransfer->setIsActive($configTransfer->getIsActive());
//        }
    }

    protected function mapMolliePaymentMethodTransfersToMollieId(MolliePaymentMethodCollectionTransfer $collection): array
    {
        $mapped = [];
        foreach ($collection->getMethods() as $paymentMethod) {
            $mapped[$paymentMethod->getId()] = $paymentMethod;
        }

        return $mapped;
    }

    /**
     * @param Request $request
     *
     * @return MolliePaymentMethodsApiResponseTransfer
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
}
