<?php

declare(strict_types = 1);

namespace MollieTest\Zed\Mollie\Business;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Zed\Mollie\Business\MollieBusinessFactory;
use Mollie\Zed\Mollie\Business\MollieFacade;
use Mollie\Zed\Mollie\Business\MollieFacadeInterface;
use Mollie\Zed\Mollie\MollieConfig;
use Mollie\Zed\Mollie\MollieDependencyProvider;
use Mollie\Zed\Mollie\Persistence\MollieRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Spryker\Service\Container\Container;
use Spryker\Shared\Kernel\Container\GlobalContainer;
use Spryker\Zed\Kernel\Container as ZedContainer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class AbstractBusinessTest extends Unit
{
    /**
     * @var string
     */
    protected const CLIENT_IP = '127.0.0.1';

    /**
     * @var string
     */
    public const TEST_STATE_MACHINE_NAME = 'Test01';

    protected MollieFacadeInterface $mollieFacade;

    protected MollieBusinessFactory $businessFactory;

    protected MollieClientInterface $mollieClient;

    protected MollieConfig $mollieConfig;

    /**
     * @var \Generated\Shared\Transfer\QuoteTransfer
     */
    protected $quoteTransfer;

    /**
     * @var \Generated\Shared\Transfer\OrderTransfer
     */
    protected $orderTransfer;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->mollieFacade = new MollieFacade();

        $globalContainer = new GlobalContainer();
        $globalContainer->setContainer(new Container([
            'request_stack' => $this->getRequestStackMock(),
        ]));
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\HttpFoundation\RequestStack
     */
    protected function getRequestStackMock(): RequestStack
    {
        $mock = $this->getMockBuilder(RequestStack::class)
            ->getMock();

        $mock->method('getCurrentRequest')->willReturn($this->getCurrentRequestMock());

        return $mock;
    }

      /**
       * @return \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\HttpFoundation\Request
       */
    protected function getCurrentRequestMock(): Request
    {
        $mock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock->method('getClientIp')->willReturn(self::CLIENT_IP);

        return $mock;
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\MollieBusinessFactory
     */
    protected function createMollieBusinessFactory(): MollieBusinessFactory
    {
        $businessFactory = $this->getMockBuilder(MollieBusinessFactory::class)
            ->onlyMethods(['getMollieClient'])
            ->getMock();

        $businessFactory->setConfig($this->mollieConfig);
        $businessFactory->setRepository($this->createMollieRepository());

        $dependencyProvider = new MollieDependencyProvider();
        $container = new ZedContainer();
        $dependencyProvider->provideBusinessLayerDependencies($container);
        $businessFactory->setContainer($container);

        $businessFactory->method('getMollieClient')
            ->willReturn($this->mollieClient);

        return $businessFactory;
    }

    /**
     * @return \Mollie\Zed\Mollie\Persistence\MollieRepository
     */
    protected function createMollieRepository(): MollieRepository
    {
        $mollieRepository = $this->getMockBuilder(MollieRepository::class)
            ->onlyMethods(['getPaymentMethodConfigCollection'])
            ->getMock();

        $mollieRepository->method('getPaymentMethodConfigCollection')
            ->willReturn(new MolliePaymentMethodConfigCollectionTransfer());

        return $mollieRepository;
    }

    /**
     * @return \Mollie\Client\Mollie\MollieClientInterface
     */
    protected function createMollieClientMock(): MollieClientInterface
    {
        return $this->getMockBuilder(MollieClientInterface::class)->getMock();
    }

    /**
     * @param bool $isTestMode
     *
     * @return \Mollie\Zed\Mollie\MollieConfig|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function createMollieConfigMock(bool $isTestMode = false): MollieConfig|MockObject
    {
        $configMock = $this->getMockBuilder(MollieConfig::class)
            ->onlyMethods(['isTestMode'])
            ->getMock();

        $configMock->method('isTestMode')
            ->willReturn($isTestMode);

        return $configMock;
    }
}
