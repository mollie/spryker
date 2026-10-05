<?php


declare(strict_types=1);

namespace MollieTest\Zed\Mollie\Business\ExpressCheckout;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundApiResponseTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Zed\Mollie\Business\ExpressCheckout\Refund\ExpressCheckoutPaymentRefunder;
use Mollie\Zed\Mollie\Dependency\MollieToStorageClientInterface;
use Mollie\Zed\Mollie\MollieConfig;

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
    public function testRefundReportsUnknownPaymentWhenWebhookHasNotArrived(): void
    {
        $mollieClientMock = $this->createMock(MollieClientInterface::class);
        $mollieClientMock->expects($this->never())->method('createRefund');

        $response = $this->createRefunder($mollieClientMock, null)->refund($this->createRequest());

        $this->assertFalse($response->getIsSuccessful());
        $this->assertFalse($response->getIsPaymentKnown());
    }

    /**
     * @return void
     */
    public function testRefundRefundsFullPaidAmountIdempotently(): void
    {
        $refundRequest = null;
        $mollieClientMock = $this->createMock(MollieClientInterface::class);
        $mollieClientMock->method('getPaymentByTransactionId')->willReturn(
            (new MolliePaymentApiResponseTransfer())->setIsSuccessful(true)->setMolliePayment(
                (new MolliePaymentTransfer())->setAmount((new MollieAmountTransfer())->setCurrency('EUR')->setValue('0.02')),
            ),
        );
        $mollieClientMock->method('createRefund')->willReturnCallback(
            function (MollieApiRequestTransfer $mollieApiRequestTransfer) use (&$refundRequest) {
                $refundRequest = $mollieApiRequestTransfer;

                return (new MollieRefundApiResponseTransfer())->setIsSuccessful(true);
            },
        );

        $response = $this->createRefunder($mollieClientMock, ['transactionId' => 'tr_riJSrDBms8mKZ5QCrxiXJ', 'status' => 'paid'])
            ->refund($this->createRequest());

        $this->assertTrue($response->getIsSuccessful());
        $this->assertSame(static::UUID, $refundRequest->getIdempotencyKey());
        $this->assertSame('tr_riJSrDBms8mKZ5QCrxiXJ', $refundRequest->getRefund()->getTransactionId());
        $this->assertSame('2', $refundRequest->getRefund()->getAmount()->getValue());
        $this->assertSame('EUR', $refundRequest->getRefund()->getAmount()->getCurrency());
    }

    /**
     * @return void
     */
    public function testRefundFailsWhenMollieRejectsTheRefund(): void
    {
        $mollieClientMock = $this->createMock(MollieClientInterface::class);
        $mollieClientMock->method('getPaymentByTransactionId')->willReturn(
            (new MolliePaymentApiResponseTransfer())->setMolliePayment(
                (new MolliePaymentTransfer())->setAmount((new MollieAmountTransfer())->setCurrency('EUR')->setValue('0.02')),
            ),
        );
        $mollieClientMock->method('createRefund')->willReturn(
            (new MollieRefundApiResponseTransfer())->setIsSuccessful(false)->setMessage('Refund rejected'),
        );

        $response = $this->createRefunder($mollieClientMock, ['transactionId' => 'tr_x', 'status' => 'paid'])->refund($this->createRequest());

        $this->assertFalse($response->getIsSuccessful());
        $this->assertTrue($response->getIsPaymentKnown());
    }

    /**
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     * @param array<string, string>|null $pendingPayment
     *
     * @return \Mollie\Zed\Mollie\Business\ExpressCheckout\Refund\ExpressCheckoutPaymentRefunder
     */
    protected function createRefunder(MollieClientInterface $mollieClient, ?array $pendingPayment): ExpressCheckoutPaymentRefunder
    {
        $storageClientMock = $this->createMock(MollieToStorageClientInterface::class);
        $storageClientMock->method('get')->willReturn($pendingPayment);

        return new ExpressCheckoutPaymentRefunder($mollieClient, $storageClientMock, new MollieConfig());
    }

    /**
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer
     */
    protected function createRequest(): MollieExpressCheckoutPaymentUpdateRequestTransfer
    {
        return (new MollieExpressCheckoutPaymentUpdateRequestTransfer())->setExpressCheckoutUuid(static::UUID);
    }
}
