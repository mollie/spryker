<?php

namespace MollieTest\Zed\Mollie\Business\Order;

use Generated\Shared\Transfer\OrderCollectionRequestTransfer;
use Generated\Shared\Transfer\OrderCollectionResponseTransfer;
use MollieTest\Zed\Mollie\Business\AbstractBusinessTest;
use Spryker\Service\Container\Container;
use Spryker\Shared\Kernel\Container\GlobalContainer;

class OrderUpdateCollectionTest extends AbstractBusinessTest
{
    /**
     * @var \MollieTest\Zed\Mollie\MollieZedTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $globalContainer = new GlobalContainer();
        $globalContainer->setContainer(new Container([
            'request_stack' => $this->getRequestStackMock(),
        ]));
    }

    //TODO: test will be finalized once the webhook integration is finished
    /**
     * @return void
     */
    public function testUpdateOrderCollectionSuccessfullyUpdatesOrder(): void
    {
        // Arrange
        $saveOrderTransfer = $this->tester->createOrder();
        $molliePaymentEntity = $this->tester->createMolliePayment($saveOrderTransfer->getIdSalesOrder());
        $orderCollectionRequestTransfer = (new OrderCollectionRequestTransfer())
            ->setId($molliePaymentEntity->getTransactionId())
            ->setStatus($molliePaymentEntity->getStatus());

        //Act
        $response = $this->mollieFacade->updateOrderCollection($orderCollectionRequestTransfer);

        //Assert
        $this->assertInstanceOf(OrderCollectionResponseTransfer::class, $response);
        $this->assertEquals(true, $response->getIsSuccess());
    }
}
