<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\SingleClick;

use ArrayObject;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieCustomerTransfer;
use Generated\Shared\Transfer\MollieSingleClickPaymentDataRequestTransfer;
use Generated\Shared\Transfer\MollieSingleClickPaymentDataTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Spryker\Shared\Log\LoggerTrait;

class SavedCardReader implements SavedCardReaderInterface
{
    use LoggerTrait;

    /**
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     * @param \Mollie\Zed\Mollie\Business\SingleClick\SingleClickConfigReaderInterface $singleClickConfigReader
     * @param \Mollie\Zed\Mollie\Business\SingleClick\MollieCustomerProviderInterface $mollieCustomerProvider
     */
    public function __construct(
        protected MollieClientInterface $mollieClient,
        protected SingleClickConfigReaderInterface $singleClickConfigReader,
        protected MollieCustomerProviderInterface $mollieCustomerProvider,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\MollieSingleClickPaymentDataRequestTransfer $mollieSingleClickPaymentDataRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieSingleClickPaymentDataTransfer
     */
    public function getSingleClickPaymentData(
        MollieSingleClickPaymentDataRequestTransfer $mollieSingleClickPaymentDataRequestTransfer,
    ): MollieSingleClickPaymentDataTransfer {
        $mollieSingleClickPaymentDataTransfer = (new MollieSingleClickPaymentDataTransfer())
            ->setIsEnabled($this->singleClickConfigReader->isSingleClickPaymentEnabled());

        if (!$mollieSingleClickPaymentDataTransfer->getIsEnabled()) {
            return $mollieSingleClickPaymentDataTransfer;
        }

        return $this->addCustomerSavedCards(
            $mollieSingleClickPaymentDataTransfer,
            $mollieSingleClickPaymentDataRequestTransfer->getIdCustomer(),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\MollieSingleClickPaymentDataRequestTransfer $mollieSingleClickPaymentDataRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieSingleClickPaymentDataTransfer
     */
    public function getCustomerSavedCards(
        MollieSingleClickPaymentDataRequestTransfer $mollieSingleClickPaymentDataRequestTransfer,
    ): MollieSingleClickPaymentDataTransfer {
        $mollieSingleClickPaymentDataTransfer = (new MollieSingleClickPaymentDataTransfer())
            ->setIsEnabled($this->singleClickConfigReader->isSingleClickPaymentEnabled());

        return $this->addCustomerSavedCards(
            $mollieSingleClickPaymentDataTransfer,
            $mollieSingleClickPaymentDataRequestTransfer->getIdCustomer(),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\MollieCustomerTransfer $mollieCustomerTransfer
     *
     * @return \ArrayObject<int, \Generated\Shared\Transfer\MollieMandateTransfer>
     */
    public function getSavedCards(MollieCustomerTransfer $mollieCustomerTransfer): ArrayObject
    {
        $mollieApiRequestTransfer = (new MollieApiRequestTransfer())
            ->setMollieCustomerId($mollieCustomerTransfer->getMollieCustomerIdOrFail());

        $mollieMandateApiResponseTransfer = $this->mollieClient->getCustomerMandates($mollieApiRequestTransfer);

        if ($mollieMandateApiResponseTransfer->getIsSuccessful()) {
            return $mollieMandateApiResponseTransfer->getMandateCollectionOrFail()->getMandates();
        }

        $this->getLogger()->error('Mollie single-click: saved cards could not be fetched.', [
            'idCustomer' => $mollieCustomerTransfer->getFkCustomer(),
            'mollieCustomerId' => $mollieCustomerTransfer->getMollieCustomerId(),
            'code' => $mollieMandateApiResponseTransfer->getCode(),
            'message' => $mollieMandateApiResponseTransfer->getMessage(),
        ]);

        return new ArrayObject();
    }

    /**
     * @param \Generated\Shared\Transfer\MollieSingleClickPaymentDataTransfer $mollieSingleClickPaymentDataTransfer
     * @param int|null $idCustomer
     *
     * @return \Generated\Shared\Transfer\MollieSingleClickPaymentDataTransfer
     */
    protected function addCustomerSavedCards(
        MollieSingleClickPaymentDataTransfer $mollieSingleClickPaymentDataTransfer,
        ?int $idCustomer,
    ): MollieSingleClickPaymentDataTransfer {
        if (!$idCustomer) {
            return $mollieSingleClickPaymentDataTransfer;
        }

        $mollieCustomerTransfer = $this->mollieCustomerProvider->findMollieCustomer(
            (new CustomerTransfer())->setIdCustomer($idCustomer),
        );

        if (!$mollieCustomerTransfer) {
            return $mollieSingleClickPaymentDataTransfer;
        }

        return $mollieSingleClickPaymentDataTransfer->setSavedCards($this->getSavedCards($mollieCustomerTransfer));
    }
}
