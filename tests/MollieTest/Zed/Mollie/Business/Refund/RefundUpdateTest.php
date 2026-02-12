<?php

namespace MollieTest\Zed\Mollie\Business\Refund;

use Generated\Shared\Transfer\MolliePaymentTransfer;
use MollieTest\Zed\Mollie\Business\AbstractBusinessTest;
use Spryker\Service\UtilEncoding\UtilEncodingServiceInterface;

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
        $utilEncodingService = $this->getUtilEncodingService();
        $saveOrderTransfer = $this->tester->haveOrder([
        ], static::TEST_STATE_MACHINE_NAME);

        $refunds = $utilEncodingService->decodeJson($this->tester->getMollieRefunds(), true);

        $mollieRefundEntity = $this->tester->createMollieRefund($saveOrderTransfer->getOrderItems());
        $molliePaymentTransfer = (new MolliePaymentTransfer())
            ->setId($mollieRefundEntity->getTransactionId())
            ->setEmbedded($refunds);

        //Act
        $response = $this->mollieFacade->processRefundData($molliePaymentTransfer);
        $updatedMollieRefundEntity = $this->tester->findMollieRefund($mollieRefundEntity->getRefundId());

        //Assert
        $this->assertEquals('refunded', $updatedMollieRefundEntity->getStatus());
        $this->assertTrue($response->getIsSuccess());
    }

    /**
     * @return \Spryker\Service\UtilEncoding\UtilEncodingServiceInterface
     */
    protected function getUtilEncodingService(): UtilEncodingServiceInterface
    {
        return $this->tester->getLocator()->utilEncoding()->service();
    }
}
