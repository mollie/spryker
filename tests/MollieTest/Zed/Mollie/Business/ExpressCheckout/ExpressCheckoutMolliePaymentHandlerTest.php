<?php


declare(strict_types=1);

namespace MollieTest\Zed\Mollie\Business\ExpressCheckout;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer;
use Mollie\Zed\Mollie\Business\Handler\ExpressCheckoutMolliePaymentHandler;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;

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
        $entityManagerMock = $this->createMock(MollieEntityManagerInterface::class);
        $entityManagerMock->method('updateExpressCheckoutMolliePayment')->willReturn(false);

        $response = (new ExpressCheckoutMolliePaymentHandler($entityManagerMock))
            ->updateExpressCheckoutMolliePayment($this->createWebhookRequest());

        $this->assertFalse($response->getIsSuccessful());
    }

    /**
     * @return void
     */
    public function testWebhookAfterOrderUpdatesPaymentRow(): void
    {
        $entityManagerMock = $this->createMock(MollieEntityManagerInterface::class);
        $entityManagerMock->expects($this->once())->method('updateExpressCheckoutMolliePayment')->willReturn(true);

        $response = (new ExpressCheckoutMolliePaymentHandler($entityManagerMock))
            ->updateExpressCheckoutMolliePayment($this->createWebhookRequest());

        $this->assertTrue($response->getIsSuccessful());
    }

    /**
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer
     */
    protected function createWebhookRequest(): MollieExpressCheckoutPaymentUpdateRequestTransfer
    {
        return (new MollieExpressCheckoutPaymentUpdateRequestTransfer())
            ->setExpressCheckoutUuid('19d863d2-5eaf-4c8c-91f6-e1b9329c6c2d')
            ->setTransactionId('tr_riJSrDBms8mKZ5QCrxiXJ')
            ->setStatus('paid');
    }
}
