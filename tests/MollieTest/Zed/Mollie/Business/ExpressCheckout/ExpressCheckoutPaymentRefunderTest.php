<?php

declare(strict_types=1);

namespace MollieTest\Zed\Mollie\Business\ExpressCheckout;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer;
use Generated\Shared\Transfer\MollieRefundApiResponseTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Zed\Mollie\Business\ExpressCheckout\Refund\ExpressCheckoutPaymentRefunderInterface;
use Mollie\Zed\Mollie\Business\MollieBusinessFactory;

/**
 * @group MollieTest
 * @group Zed
 * @group Mollie
 * @group Business
 * @group ExpressCheckout
 * @group ExpressCheckoutPaymentRefunderTest
 */
class ExpressCheckoutPaymentRefunderTest extends Unit
{
    /**
     * @var string
     */
    protected const EXPRESS_CHECKOUT_UUID = '19d863d2-5eaf-4c8c-91f6-e1b9329c6c2d';

    /**
     * @var string
     */
    protected const TRANSACTION_ID = 'tr_QKZEBWM24ifxZzhqkGjXJ';

    /**
     * @var \Generated\Shared\Transfer\MollieApiRequestTransfer|null
     */
    protected ?MollieApiRequestTransfer $sentMollieApiRequestTransfer = null;

    /**
     * @return void
     */
    public function testRefundRefundsFullPaidAmountIdempotently(): void
    {
        $mollieExpressCheckoutPaymentRefundTransfer = (new MollieExpressCheckoutPaymentRefundTransfer())
            ->setExpressCheckoutUuid(static::EXPRESS_CHECKOUT_UUID)
            ->setTransactionId(static::TRANSACTION_ID)
            ->setAmount(2)
            ->setCurrency('EUR');

        $mollieRefundApiResponseTransfer = $this->createExpressCheckoutPaymentRefunder()->refund($mollieExpressCheckoutPaymentRefundTransfer);

        $this->assertTrue($mollieRefundApiResponseTransfer->getIsSuccessful());
        $this->assertSame(static::EXPRESS_CHECKOUT_UUID, $this->sentMollieApiRequestTransfer->getIdempotencyKey());

        $sentMollieRefundTransfer = $this->sentMollieApiRequestTransfer->getRefund();
        $this->assertSame(static::TRANSACTION_ID, $sentMollieRefundTransfer->getTransactionId());
        $this->assertSame('2', $sentMollieRefundTransfer->getAmount()->getValue());
        $this->assertSame('EUR', $sentMollieRefundTransfer->getAmount()->getCurrency());
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\ExpressCheckout\Refund\ExpressCheckoutPaymentRefunderInterface
     */
    protected function createExpressCheckoutPaymentRefunder(): ExpressCheckoutPaymentRefunderInterface
    {
        $mollieClientMock = $this->createMock(MollieClientInterface::class);
        $mollieClientMock->expects($this->once())->method('createRefund')->willReturnCallback(
            function (MollieApiRequestTransfer $mollieApiRequestTransfer) {
                $this->sentMollieApiRequestTransfer = $mollieApiRequestTransfer;

                return (new MollieRefundApiResponseTransfer())->setIsSuccessful(true);
            },
        );

        $businessFactoryMock = $this->getMockBuilder(MollieBusinessFactory::class)
            ->onlyMethods(['getMollieClient'])
            ->getMock();
        $businessFactoryMock->method('getMollieClient')->willReturn($mollieClientMock);

        return $businessFactoryMock->createExpressCheckoutPaymentRefunder();
    }
}
