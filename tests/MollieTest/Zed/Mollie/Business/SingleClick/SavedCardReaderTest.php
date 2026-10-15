<?php

declare(strict_types = 1);

namespace MollieTest\Zed\Mollie\Business\SingleClick;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MollieCustomerTransfer;
use Generated\Shared\Transfer\MollieMandateApiResponseTransfer;
use Generated\Shared\Transfer\MollieMandateCollectionTransfer;
use Generated\Shared\Transfer\MollieMandateTransfer;
use Generated\Shared\Transfer\MollieSingleClickPaymentDataRequestTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Zed\Mollie\Business\SingleClick\MollieCustomerProviderInterface;
use Mollie\Zed\Mollie\Business\SingleClick\SavedCardReader;
use Mollie\Zed\Mollie\Business\SingleClick\SingleClickConfigReaderInterface;

class SavedCardReaderTest extends Unit
{
    /**
     * @var int
     */
    protected const ID_CUSTOMER = 42;

    /**
     * @return void
     */
    public function testGetCustomerSavedCardsReturnsCardsWhenSingleClickIsDisabled(): void
    {
        $mollieSingleClickPaymentDataTransfer = $this->createReader(false)
            ->getCustomerSavedCards((new MollieSingleClickPaymentDataRequestTransfer())->setIdCustomer(static::ID_CUSTOMER));

        $this->assertFalse($mollieSingleClickPaymentDataTransfer->getIsEnabled());
        $this->assertCount(1, $mollieSingleClickPaymentDataTransfer->getSavedCards());
    }

    /**
     * @return void
     */
    public function testGetSingleClickPaymentDataReturnsNoCardsWhenSingleClickIsDisabled(): void
    {
        $mollieSingleClickPaymentDataTransfer = $this->createReader(false)
            ->getSingleClickPaymentData((new MollieSingleClickPaymentDataRequestTransfer())->setIdCustomer(static::ID_CUSTOMER));

        $this->assertFalse($mollieSingleClickPaymentDataTransfer->getIsEnabled());
        $this->assertCount(0, $mollieSingleClickPaymentDataTransfer->getSavedCards());
    }

    /**
     * @param bool $isSingleClickPaymentEnabled
     *
     * @return \Mollie\Zed\Mollie\Business\SingleClick\SavedCardReader
     */
    protected function createReader(bool $isSingleClickPaymentEnabled): SavedCardReader
    {
        $mollieClientMock = $this->createMock(MollieClientInterface::class);
        $mollieClientMock->method('getCustomerMandates')
            ->willReturn((new MollieMandateApiResponseTransfer())
                ->setIsSuccessful(true)
                ->setMandateCollection((new MollieMandateCollectionTransfer())->addMandate((new MollieMandateTransfer())->setId('mdt_pWUnw6pkBN'))));

        $singleClickConfigReaderMock = $this->createMock(SingleClickConfigReaderInterface::class);
        $singleClickConfigReaderMock->method('isSingleClickPaymentEnabled')->willReturn($isSingleClickPaymentEnabled);

        $mollieCustomerProviderMock = $this->createMock(MollieCustomerProviderInterface::class);
        $mollieCustomerProviderMock->method('findMollieCustomer')
            ->willReturn((new MollieCustomerTransfer())->setFkCustomer(static::ID_CUSTOMER)->setMollieCustomerId('cst_8wmqcHMN4U'));

        return new SavedCardReader($mollieClientMock, $singleClickConfigReaderMock, $mollieCustomerProviderMock);
    }
}
