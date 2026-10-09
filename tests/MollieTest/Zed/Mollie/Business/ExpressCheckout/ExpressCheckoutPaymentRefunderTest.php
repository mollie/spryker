<?php


declare(strict_types=1);

namespace MollieTest\Zed\Mollie\Business\ExpressCheckout;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer;
use Generated\Shared\Transfer\MollieRefundApiResponseTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Zed\Mollie\Business\ExpressCheckout\Refund\ExpressCheckoutPaymentRefunder;

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
    protected const UUID = '19d863d2-5eaf-4c8c-91f6-e1b9329c6c2d';

    /**
     * @return void
     */
    public function testRefundRefundsFullPaidAmountIdempotently(): void
    {
        $refundRequest = null;
        $mollieClientMock = $this->createMock(MollieClientInterface::class);
        $mollieClientMock->expects($this->once())->method('createRefund')->willReturnCallback(
            function (MollieApiRequestTransfer $mollieApiRequestTransfer) use (&$refundRequest) {
                $refundRequest = $mollieApiRequestTransfer;

                return (new MollieRefundApiResponseTransfer())->setIsSuccessful(true);
            },
        );

        $response = (new ExpressCheckoutPaymentRefunder($mollieClientMock))->refund(
            (new MollieExpressCheckoutFailedOrderTransfer())
                ->setExpressCheckoutUuid(static::UUID)
                ->setTransactionId('tr_QKZEBWM24ifxZzhqkGjXJ')
                ->setAmount(2)
                ->setCurrency('EUR'),
        );

        $this->assertTrue($response->getIsSuccessful());
        $this->assertSame(static::UUID, $refundRequest->getIdempotencyKey());
        $this->assertSame('tr_QKZEBWM24ifxZzhqkGjXJ', $refundRequest->getRefund()->getTransactionId());
        $this->assertSame('2', $refundRequest->getRefund()->getAmount()->getValue());
        $this->assertSame('EUR', $refundRequest->getRefund()->getAmount()->getCurrency());
    }
}
