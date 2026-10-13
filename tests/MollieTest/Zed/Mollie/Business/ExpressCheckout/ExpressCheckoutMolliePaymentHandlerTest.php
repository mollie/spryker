<?php

declare(strict_types=1);

namespace MollieTest\Zed\Mollie\Business\ExpressCheckout;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer;
use Mollie\Zed\Mollie\Business\Handler\ExpressCheckoutMolliePaymentHandlerInterface;
use Mollie\Zed\Mollie\Business\MollieBusinessFactory;
use Mollie\Zed\Mollie\Persistence\MollieEntityManager;

/**
 * @group MollieTest
 * @group Zed
 * @group Mollie
 * @group Business
 * @group ExpressCheckout
 * @group ExpressCheckoutMolliePaymentHandlerTest
 */
class ExpressCheckoutMolliePaymentHandlerTest extends Unit
{
    /**
     * @return void
     */
    public function testWebhookBeforeOrderIsNotSuccessfulSoMollieRetries(): void
    {
        $mollieExpressCheckoutPaymentUpdateResponseTransfer = $this->createExpressCheckoutMolliePaymentHandler(false)
            ->updateExpressCheckoutMolliePayment($this->createMollieExpressCheckoutPaymentUpdateRequestTransfer());

        $this->assertFalse($mollieExpressCheckoutPaymentUpdateResponseTransfer->getIsExpressPaymentFound());
    }

    /**
     * @return void
     */
    public function testWebhookAfterOrderUpdatesPaymentRow(): void
    {
        $mollieExpressCheckoutPaymentUpdateResponseTransfer = $this->createExpressCheckoutMolliePaymentHandler(true)
            ->updateExpressCheckoutMolliePayment($this->createMollieExpressCheckoutPaymentUpdateRequestTransfer());

        $this->assertTrue($mollieExpressCheckoutPaymentUpdateResponseTransfer->getIsExpressPaymentFound());
    }

    /**
     * @param bool $isPaymentRowUpdated
     *
     * @return \Mollie\Zed\Mollie\Business\Handler\ExpressCheckoutMolliePaymentHandlerInterface
     */
    protected function createExpressCheckoutMolliePaymentHandler(bool $isPaymentRowUpdated): ExpressCheckoutMolliePaymentHandlerInterface
    {
        $entityManagerMock = $this->createMock(MollieEntityManager::class);
        $entityManagerMock->expects($this->once())
            ->method('updateExpressCheckoutMolliePayment')
            ->willReturn($isPaymentRowUpdated);

        $businessFactory = new MollieBusinessFactory();
        $businessFactory->setEntityManager($entityManagerMock);

        return $businessFactory->createExpressCheckoutMolliePaymentHandler();
    }

    /**
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer
     */
    protected function createMollieExpressCheckoutPaymentUpdateRequestTransfer(): MollieExpressCheckoutPaymentUpdateRequestTransfer
    {
        return (new MollieExpressCheckoutPaymentUpdateRequestTransfer())
            ->setExpressCheckoutUuid('19d863d2-5eaf-4c8c-91f6-e1b9329c6c2d')
            ->setTransactionId('tr_riJSrDBms8mKZ5QCrxiXJ')
            ->setStatus('paid');
    }
}
