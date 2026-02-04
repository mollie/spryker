<?php


declare(strict_types = 1);

namespace MollieTest\Zed\Mollie\Business;

use Codeception\Test\Unit;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Zed\Mollie\Business\MollieBusinessFactory;
use Mollie\Zed\Mollie\Business\MollieFacade;
use Mollie\Zed\Mollie\Business\MollieFacadeInterface;
use Spryker\Service\Container\Container;
use Spryker\Shared\Kernel\Container\GlobalContainer;
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
        $this->businessFactory = $this->createMock(MollieBusinessFactory::class);
        $this->mollieClient = $this->createMock(MollieClientInterface::class);
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
}
