<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Communication\Table\TableDataProvider;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieAvailablePaymentMethodsApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Mollie\Shared\Mollie\MollieConstants;
use Mollie\Zed\Mollie\Business\MollieFacadeInterface;
use Mollie\Zed\Mollie\Communication\Mapper\MollieCommunicationMapperInterface;
use Mollie\Zed\Mollie\Dependency\MollieToStorageClientInterface;
use Mollie\Zed\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;

class MolliePaymentMethodsDataProvider
{
    public function __construct(
        private MollieFacadeInterface $mollieFacade,
        private MollieCommunicationMapperInterface $mapper,
        private MollieToStorageClientInterface $storageClient,
        private MollieToUtilEncodingServiceInterface $utilEncodingService,
    ) {
    }

    public function getData()
    {
        $cached = $this->getCachedData();
        if ($cached) {
            return $cached;
        }

        $requestTransfer = (new MollieApiRequestTransfer())
            ->setMolliePaymentMethodQueryParameters(
                (new MolliePaymentMethodQueryParametersTransfer())
                    ->setSequenceType('oneoff'),
            );

        $responseTransfer = $this->mollieFacade->getAvailablePaymentMethods($requestTransfer);
        if ($responseTransfer->getIsSuccessful()) {
            $this->setPaymentMethodsData($responseTransfer);
        }

        return $responseTransfer;
    }

    /**
     * @return mixed
     */
    protected function getCachedData()
    {
        return $this->storageClient->get(MollieConstants::MOLLIE_AVAILABLE_METHODS_STORAGE_KEY);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieAvailablePaymentMethodsApiResponseTransfer $responseTransfer
     *
     * @return void
     */
    protected function setPaymentMethodsData(MollieAvailablePaymentMethodsApiResponseTransfer $responseTransfer): void
    {
            $paymentMethodCollection = $responseTransfer->getCollection();
            $paymentMethodData = [];

            foreach ($paymentMethodCollection->getMethods()->getArrayCopy() as $paymentMethodTransfer) {
                $paymentMethodData[] = $paymentMethodTransfer->toArray(true, true);
            }

            $encodedPaymentMethodData = $this->utilEncodingService->encodeJson($paymentMethodData);
            $this->storageClient->set(MollieConstants::MOLLIE_AVAILABLE_METHODS_STORAGE_KEY, $encodedPaymentMethodData);
    }
}
