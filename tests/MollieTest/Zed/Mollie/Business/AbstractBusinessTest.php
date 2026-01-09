<?php


declare(strict_types = 1);

namespace MollieTest\Zed\Mollie\Business;

use Codeception\Test\Unit;
use Mollie\Zed\Mollie\Business\MollieFacade;
use Mollie\Zed\Mollie\Business\MollieFacadeInterface;
use Mollie\Zed\Mollie\Persistence\MollieEntityManager;
use Mollie\Zed\Mollie\Persistence\MollieRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class AbstractBusinessTest extends Unit
{
    /**
     * @var string
     */
    protected const CLIENT_IP = '127.0.0.1';

    protected MollieFacadeInterface $mollieFacade;

    protected MollieEntityManager $mollieEntityManager;

    protected MollieRepository $mollieRepository;

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
