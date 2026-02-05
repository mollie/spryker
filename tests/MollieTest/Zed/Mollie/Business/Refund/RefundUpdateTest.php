<?php

namespace MollieTest\Zed\Mollie\Business\Refund;

use Generated\Shared\Transfer\MollieRefundTransfer;
use MollieTest\Zed\Mollie\Business\AbstractBusinessTest;

class RefundUpdateTest extends AbstractBusinessTest
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
    public function testWebHookRequestSuccessfullyUpdatesMollieRefund(): void
    {
        // Arrange
        $saveOrderTransfer = $this->tester->haveOrder([
        ], static::TEST_STATE_MACHINE_NAME);

        $mollieRefundEntity = $this->tester->createMollieRefund($saveOrderTransfer->getOrderItems());
        $mollieRefundTransfer = (new MollieRefundTransfer())
            ->setId($mollieRefundEntity->getRefundId())
            ->setStatus('refunded');

        //Act
        $response = $this->mollieFacade->processRefundData($mollieRefundTransfer);
        $updatedMollieRefundEntity = $this->tester->findMollieRefund($mollieRefundEntity->getRefundId());

        //Assert
        $this->assertEquals('refunded', $updatedMollieRefundEntity->getStatus());
        $this->assertTrue($response->getIsSuccess());
    }
}
