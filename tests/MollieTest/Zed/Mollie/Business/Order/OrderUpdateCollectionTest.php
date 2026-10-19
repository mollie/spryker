<?php


declare(strict_types = 1);

namespace MollieTest\Zed\Mollie\Business\Order;

use Generated\Shared\Transfer\OrderCollectionRequestTransfer;
use MollieTest\Zed\Mollie\Business\AbstractBusinessTest;

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
        $this->tester->configureTestStateMachine([static::TEST_STATE_MACHINE_NAME]);
    }

    /**
     * @return void
     */
    public function testWebHookRequestSuccessfullyUpdatesMolliePayment(): void
    {
        // Arrange
        $saveOrderTransfer = $this->tester->haveOrder([
        ], static::TEST_STATE_MACHINE_NAME);

        $molliePaymentEntity = $this->tester->createMolliePayment($saveOrderTransfer->getIdSalesOrder());
        $orderCollectionRequestTransfer = (new OrderCollectionRequestTransfer())
            ->setId($molliePaymentEntity->getTransactionId())
            ->setStatus('paid');

        //Act
        $response = $this->mollieFacade->updateOrderCollection($orderCollectionRequestTransfer);
        $updatedMolliePaymentEntity = $this->tester->findMolliePayment($molliePaymentEntity->getFkSalesOrder());

        //Assert
        $this->assertEquals('paid', $updatedMolliePaymentEntity->getStatus());
        $this->assertTrue($response->getIsSuccess());
    }
}
