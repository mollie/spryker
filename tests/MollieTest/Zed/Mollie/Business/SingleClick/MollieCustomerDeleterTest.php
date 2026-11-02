<?php

declare(strict_types = 1);

namespace MollieTest\Zed\Mollie\Business\SingleClick;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieCustomerApiResponseTransfer;
use Generated\Shared\Transfer\MollieCustomerTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Zed\Mollie\Business\MollieBusinessFactory;
use Mollie\Zed\Mollie\Business\SingleClick\MollieCustomerDeleterInterface;
use Mollie\Zed\Mollie\MollieConfig;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;
use Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface;

class MollieCustomerDeleterTest extends Unit
{
    /**
     * @var int
     */
    protected const ID_CUSTOMER = 42;

    /**
     * @var int
     */
    protected const ID_MOLLIE_CUSTOMER = 7;

    /**
     * @var string
     */
    protected const MOLLIE_CUSTOMER_ID = 'cst_8wmqcHMN4U';

    /**
     * @return void
     */
    public function testDeleteMollieCustomerDoesNothingWhenCustomerHasNoMollieCustomer(): void
    {
        $mollieClientMock = $this->createMock(MollieClientInterface::class);
        $mollieClientMock->expects($this->never())->method('deleteCustomer');

        $entityManagerMock = $this->createMock(MollieEntityManagerInterface::class);
        $entityManagerMock->expects($this->never())->method('deleteMollieCustomer');

        $this->createMollieCustomerDeleter($mollieClientMock, null, $entityManagerMock)
            ->deleteMollieCustomer($this->createCustomerTransfer());
    }

    /**
     * @return void
     */
    public function testDeleteMollieCustomerDeletesAtMollieAndRemovesStoredCustomer(): void
    {
        $mollieClientMock = $this->createMock(MollieClientInterface::class);
        $mollieClientMock->expects($this->once())
            ->method('deleteCustomer')
            ->with($this->callback(function (MollieApiRequestTransfer $mollieApiRequestTransfer): bool {
                return $mollieApiRequestTransfer->getMollieCustomerId() === static::MOLLIE_CUSTOMER_ID;
            }))
            ->willReturn((new MollieCustomerApiResponseTransfer())->setIsSuccessful(true));

        $entityManagerMock = $this->createMock(MollieEntityManagerInterface::class);
        $entityManagerMock->expects($this->once())
            ->method('deleteMollieCustomer')
            ->with(static::ID_MOLLIE_CUSTOMER);

        $this->createMollieCustomerDeleter($mollieClientMock, $this->createMollieCustomerTransfer(), $entityManagerMock)
            ->deleteMollieCustomer($this->createCustomerTransfer());
    }

    /**
     * @return void
     */
    public function testDeleteMollieCustomerKeepsStoredCustomerWhenMollieRejectsTheDeletion(): void
    {
        $mollieClientMock = $this->createMock(MollieClientInterface::class);
        $mollieClientMock->method('deleteCustomer')
            ->willReturn((new MollieCustomerApiResponseTransfer())->setIsSuccessful(false)->setCode(500));

        $entityManagerMock = $this->createMock(MollieEntityManagerInterface::class);
        $entityManagerMock->expects($this->never())->method('deleteMollieCustomer');

        $this->createMollieCustomerDeleter($mollieClientMock, $this->createMollieCustomerTransfer(), $entityManagerMock)
            ->deleteMollieCustomer($this->createCustomerTransfer());
    }

    /**
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     * @param \Generated\Shared\Transfer\MollieCustomerTransfer|null $storedMollieCustomerTransfer
     * @param \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface $entityManager
     *
     * @return \Mollie\Zed\Mollie\Business\SingleClick\MollieCustomerDeleterInterface
     */
    protected function createMollieCustomerDeleter(
        MollieClientInterface $mollieClient,
        ?MollieCustomerTransfer $storedMollieCustomerTransfer,
        MollieEntityManagerInterface $entityManager,
    ): MollieCustomerDeleterInterface {
        $repositoryMock = $this->createMock(MollieRepositoryInterface::class);
        $repositoryMock->method('findMollieCustomer')
            ->with(static::ID_CUSTOMER)
            ->willReturn($storedMollieCustomerTransfer);

        $mollieBusinessFactoryMock = $this->getMockBuilder(MollieBusinessFactory::class)
            ->onlyMethods(['getMollieClient', 'getRepository', 'getEntityManager'])
            ->getMock();
        $mollieBusinessFactoryMock->method('getMollieClient')->willReturn($mollieClient);
        $mollieBusinessFactoryMock->method('getRepository')->willReturn($repositoryMock);
        $mollieBusinessFactoryMock->method('getEntityManager')->willReturn($entityManager);
        $mollieBusinessFactoryMock->setConfig($this->createMock(MollieConfig::class));

        return $mollieBusinessFactoryMock->createMollieCustomerDeleter();
    }

    /**
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    protected function createCustomerTransfer(): CustomerTransfer
    {
        return (new CustomerTransfer())->setIdCustomer(static::ID_CUSTOMER);
    }

    /**
     * @return \Generated\Shared\Transfer\MollieCustomerTransfer
     */
    protected function createMollieCustomerTransfer(): MollieCustomerTransfer
    {
        return (new MollieCustomerTransfer())
            ->setIdMollieCustomer(static::ID_MOLLIE_CUSTOMER)
            ->setMollieCustomerId(static::MOLLIE_CUSTOMER_ID)
            ->setFkCustomer(static::ID_CUSTOMER);
    }
}
