<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Communication\Table\TableDataProvider;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieAvailablePaymentMethodsApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Mollie\Zed\Mollie\Business\MollieFacadeInterface;
use Mollie\Zed\Mollie\Dependency\MollieToStorageClientInterface;

class MolliePaymentMethodsDataProvider
{
    public function __construct(
        private MollieToStorageClientInterface $storageClient,
        private MollieFacadeInterface $mollieFacade,
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
        return $this->storageClient->get('temp');
    }

    /**
     * @param \Generated\Shared\Transfer\MollieAvailablePaymentMethodsApiResponseTransfer $responseTransfer
     *
     * @return void
     */
    protected function setPaymentMethodsData(MollieAvailablePaymentMethodsApiResponseTransfer $responseTransfer): void
    {
        $paymentMethodCollection = $responseTransfer->getCollection();
        $encoded = json_encode($paymentMethodCollection->getMethods()->getArrayCopy());
        $this->storageClient->set('temp', $encoded);
    }
}
