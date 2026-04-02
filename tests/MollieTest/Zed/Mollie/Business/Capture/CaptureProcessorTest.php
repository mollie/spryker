<?php


declare(strict_types = 1);

namespace MollieTest\Zed\Mollie\Business\Capture;

use Generated\Shared\Transfer\MolliePaymentTransfer;
use MollieTest\Zed\Mollie\Business\AbstractBusinessTest;
use Orm\Zed\Mollie\Persistence\SpyPaymentMollie;

class CaptureProcessorTest extends AbstractBusinessTest
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
    public function testWebHookRequestSuccessfullyUpdatesMolliePaymentCaptureCollection(): void
    {
        // Arrange
        $saveOrderTransfer = $this->tester->haveOrder([
        ], static::TEST_STATE_MACHINE_NAME);

        $molliePaymentEntity = $this->tester->createMolliePayment($saveOrderTransfer->getIdSalesOrder());
        foreach ($saveOrderTransfer->getOrderItems() as $orderItem) {
            $this->tester->createMollieOrderItemPaymentCapture($orderItem->getIdSalesOrderItem());
        }

        $molliePaymentTransfer = $this->createMolliePaymentTransfer($molliePaymentEntity);

        //Act
        $molliePaymentCaptureResponseTransfer = $this->mollieFacade->updatePaymentCaptureCollection($molliePaymentTransfer);

        //Assert
        $captureEntities = $this->tester->findMollieOrderItemPaymentCaptureCollection($molliePaymentEntity->getTransactionId());

        $this->assertTrue($molliePaymentCaptureResponseTransfer->getIsSuccessful());

        foreach ($captureEntities as $captureEntity) {
            $this->assertEquals('succeeded', $captureEntity->getStatus());
        }
    }

    /**
     * @param \Orm\Zed\Mollie\Persistence\SpyPaymentMollie $molliePaymentEntity
     *
     * @return \Generated\Shared\Transfer\MolliePaymentTransfer
     */
    protected function createMolliePaymentTransfer(SpyPaymentMollie $molliePaymentEntity): MolliePaymentTransfer
    {
        $molliePaymentTransfer = new MolliePaymentTransfer();
        $molliePaymentTransfer
            ->setId($molliePaymentEntity->getTransactionId());

        $captureEntities = $this->tester->findMollieOrderItemPaymentCaptureCollection($molliePaymentEntity->getTransactionId());

        $captures = [];
        foreach ($captureEntities as $captureEntity) {
            $captures[] = [
                'resource' => 'capture',
                'id' => $captureEntity->getCaptureId(),
                'status' => 'succeeded',
            ];
        }

        $molliePaymentTransfer->setEmbedded(['captures' => $captures]);

        return $molliePaymentTransfer;
    }
}
