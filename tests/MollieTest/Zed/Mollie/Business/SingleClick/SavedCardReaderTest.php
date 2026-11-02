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
use Mollie\Zed\Mollie\Business\MollieBusinessFactory;
use Mollie\Zed\Mollie\Business\SingleClick\SavedCardReaderInterface;
use Mollie\Zed\Mollie\MollieConfig;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;
use Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface;

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
     * @return \Mollie\Zed\Mollie\Business\SingleClick\SavedCardReaderInterface
     */
    protected function createReader(bool $isSingleClickPaymentEnabled): SavedCardReaderInterface
    {
        $mollieClientMock = $this->createMock(MollieClientInterface::class);
        $mollieClientMock->method('getCustomerMandates')
            ->willReturn((new MollieMandateApiResponseTransfer())
                ->setIsSuccessful(true)
                ->setMandateCollection((new MollieMandateCollectionTransfer())->addMandate((new MollieMandateTransfer())->setId('mdt_pWUnw6pkBN'))));

        $repositoryMock = $this->createMock(MollieRepositoryInterface::class);
        $repositoryMock->method('findSingleClickConfig')->willReturn(null);
        $repositoryMock->method('findMollieCustomer')
            ->willReturn((new MollieCustomerTransfer())->setFkCustomer(static::ID_CUSTOMER)->setMollieCustomerId('cst_8wmqcHMN4U'));

        $configMock = $this->createMock(MollieConfig::class);
        $configMock->method('isSingleClickPaymentEnabledByDefault')->willReturn($isSingleClickPaymentEnabled);

        $mollieBusinessFactoryMock = $this->getMockBuilder(MollieBusinessFactory::class)
            ->onlyMethods(['getMollieClient', 'getRepository', 'getEntityManager'])
            ->getMock();
        $mollieBusinessFactoryMock->method('getMollieClient')->willReturn($mollieClientMock);
        $mollieBusinessFactoryMock->method('getRepository')->willReturn($repositoryMock);
        $mollieBusinessFactoryMock->method('getEntityManager')->willReturn($this->createMock(MollieEntityManagerInterface::class));
        $mollieBusinessFactoryMock->setConfig($configMock);

        return $mollieBusinessFactoryMock->createSavedCardReader();
    }
}
