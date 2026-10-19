<?php

declare(strict_types = 1);

namespace MollieTest\Zed\Mollie\Business\SingleClick;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\MollieCustomerApiResponseTransfer;
use Generated\Shared\Transfer\MollieCustomerTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Zed\Mollie\Business\SingleClick\MollieCustomerProvider;
use Mollie\Zed\Mollie\MollieConfig;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;
use Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;

class MollieCustomerProviderTest extends Unit
{
    /**
     * @var int
     */
    protected const ID_CUSTOMER = 42;

    /**
     * @var string
     */
    protected const MOLLIE_CUSTOMER_ID = 'cst_8wmqcHMN4U';

    /**
     * @return void
     */
    public function testCreateMollieCustomerUsesStoredCustomerWithoutCallingMollie(): void
    {
        $mollieClientMock = $this->createMock(MollieClientInterface::class);
        $mollieClientMock->expects($this->never())->method('createCustomer');

        $entityManagerMock = $this->createEntityManagerMock();
        $entityManagerMock->expects($this->never())->method('saveMollieCustomer');

        $quoteTransfer = $this->createQuoteTransfer();
        $this->createProvider($mollieClientMock, $this->createStoredCustomerTransfer(), $entityManagerMock)
            ->createMollieCustomer($quoteTransfer);

        $this->assertSame(static::MOLLIE_CUSTOMER_ID, $quoteTransfer->getCustomer()->getMollieCustomer()->getMollieCustomerId());
    }

    /**
     * @return void
     */
    public function testCreateMollieCustomerCreatesAndStoresCustomerWithConsentWhenNoneIsStored(): void
    {
        $mollieClientMock = $this->createMock(MollieClientInterface::class);
        $mollieClientMock->expects($this->once())
            ->method('createCustomer')
            ->willReturn((new MollieCustomerApiResponseTransfer())
                ->setIsSuccessful(true)
                ->setMollieCustomer((new MollieCustomerTransfer())->setMollieCustomerId(static::MOLLIE_CUSTOMER_ID)));

        $entityManagerMock = $this->createEntityManagerMock();
        $entityManagerMock->expects($this->once())
            ->method('saveMollieCustomer')
            ->with($this->callback(function (MollieCustomerTransfer $mollieCustomerTransfer): bool {
                return $mollieCustomerTransfer->getFkCustomer() === static::ID_CUSTOMER
                    && $mollieCustomerTransfer->getMollieCustomerId() === static::MOLLIE_CUSTOMER_ID
                    && $mollieCustomerTransfer->getConsentVersion() === '1'
                    && $mollieCustomerTransfer->getConsentGivenAt() !== null;
            }));

        $quoteTransfer = $this->createQuoteTransfer();
        $this->createProvider($mollieClientMock, null, $entityManagerMock)
            ->createMollieCustomer($quoteTransfer);

        $this->assertSame(static::MOLLIE_CUSTOMER_ID, $quoteTransfer->getCustomer()->getMollieCustomer()->getMollieCustomerId());
    }

    /**
     * @return void
     */
    public function testCreateMollieCustomerSetsNoMollieCustomerWhenMollieCannotCreateIt(): void
    {
        $mollieClientMock = $this->createMock(MollieClientInterface::class);
        $mollieClientMock->method('createCustomer')
            ->willReturn((new MollieCustomerApiResponseTransfer())->setIsSuccessful(false)->setCode(422));

        $entityManagerMock = $this->createEntityManagerMock();
        $entityManagerMock->expects($this->never())->method('saveMollieCustomer');

        $quoteTransfer = $this->createQuoteTransfer();
        $this->createProvider($mollieClientMock, null, $entityManagerMock)
            ->createMollieCustomer($quoteTransfer);

        $this->assertNull($quoteTransfer->getCustomer()->getMollieCustomer());
    }

    /**
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     * @param \Generated\Shared\Transfer\MollieCustomerTransfer|null $storedCustomerTransfer
     * @param \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface $entityManager
     *
     * @return \Mollie\Zed\Mollie\Business\SingleClick\MollieCustomerProvider
     */
    protected function createProvider(
        MollieClientInterface $mollieClient,
        ?MollieCustomerTransfer $storedCustomerTransfer,
        MollieEntityManagerInterface $entityManager,
    ): MollieCustomerProvider {
        $repositoryMock = $this->createMock(MollieRepositoryInterface::class);
        $repositoryMock->method('findMollieCustomer')
            ->with(static::ID_CUSTOMER)
            ->willReturn($storedCustomerTransfer);

        $configMock = $this->createMock(MollieConfig::class);
        $configMock->method('getSingleClickConsentVersion')->willReturn('1');

        return new MollieCustomerProvider($mollieClient, $repositoryMock, $entityManager, $configMock);
    }

    /**
     * @return \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function createEntityManagerMock(): MollieEntityManagerInterface|MockObject
    {
        $entityManagerMock = $this->createMock(MollieEntityManagerInterface::class);
        $entityManagerMock->method('saveMollieCustomer')
            ->willReturnCallback(fn (MollieCustomerTransfer $mollieCustomerTransfer): MollieCustomerTransfer => $mollieCustomerTransfer);

        return $entityManagerMock;
    }

    /**
     * @return \Generated\Shared\Transfer\MollieCustomerTransfer
     */
    protected function createStoredCustomerTransfer(): MollieCustomerTransfer
    {
        return (new MollieCustomerTransfer())
            ->setIdMollieCustomer(1)
            ->setMollieCustomerId(static::MOLLIE_CUSTOMER_ID)
            ->setFkCustomer(static::ID_CUSTOMER);
    }

    /**
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function createQuoteTransfer(): QuoteTransfer
    {
        return (new QuoteTransfer())->setCustomer((new CustomerTransfer())->setIdCustomer(static::ID_CUSTOMER));
    }
}
