<?php

declare(strict_types=1);

namespace MollieTest\Yves\Mollie;

use Codeception\Test\Unit;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Yves\Mollie\Dependency\Client\MollieToQuoteClientBridge;
use Mollie\Yves\Mollie\Dependency\Client\MollieToQuoteClientInterface;
use Mollie\Yves\Mollie\Dependency\Client\MollieToStorageClientBridge;
use Mollie\Yves\Mollie\Dependency\Client\MollieToStorageClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Spryker\Client\Quote\QuoteClientInterface;

class AbstractYvesTest extends Unit
{
    protected MollieYvesTester $tester;

    protected MollieToStorageClientInterface $storageClient;

    protected MollieClientInterface|MockObject $mollieClient;

    protected MollieToQuoteClientInterface|MockObject $quoteClient;

    /**
     * @return \Mollie\Yves\Mollie\Dependency\Client\MollieToStorageClientBridge
     */
    public function createMollieToStorageClientBridgeMock(): MollieToStorageClientBridge
    {
        return $this->getMockBuilder(MollieToStorageClientBridge::class)
            ->onlyMethods(['get', 'set'])
            ->setConstructorArgs([$this->tester->getStorageClient()])
            ->getMock();
    }

    /**
     * @return \Mollie\Client\Mollie\MollieClientInterface
     */
    protected function createMollieClientMock(): MollieClientInterface
    {
        return $this->getMockBuilder(MollieClientInterface::class)->getMock();
    }

    /**
     * @return \Mollie\Yves\Mollie\Dependency\Client\MollieToQuoteClientInterface
     */
    protected function createMollieToQuoteClientMock(): MollieToQuoteClientInterface
    {
        $quoteClient = $this->getMockBuilder(QuoteClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $this->getMockBuilder(MollieToQuoteClientBridge::class)
            ->onlyMethods(['setQuote', 'getQuote'])
            ->setConstructorArgs([$quoteClient])
            ->getMock();
    }
}
