<?php


declare(strict_types=1);

namespace MollieTest\Zed\Mollie\Business\ExpressCheckout;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer;
use Generated\Shared\Transfer\MollieExpressPaymentTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Mollie\Zed\Mollie\Business\Handler\ExpressCheckoutMolliePaymentHandler;
use Mollie\Zed\Mollie\Dependency\MollieToStorageClientInterface;
use Mollie\Zed\Mollie\MollieConfig;
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
     * @var string
     */
    protected const UUID = '19d863d2-5eaf-4c8c-91f6-e1b9329c6c2d';

    /**
     * @var string
     */
    protected const STORAGE_KEY = 'mollie:express-checkout:payment:' . self::UUID;

    /**
     * @var array<string, mixed>
     */
    protected array $storage = [];

    /**
     * @return void
     */
    public function testWebhookBeforeOrderRemembersPaymentAndOrderCreationAppliesIt(): void
    {
        $updates = [];
        $entityManagerMock = $this->createMock(MollieEntityManagerInterface::class);
        $entityManagerMock->method('updateExpressCheckoutMolliePayment')->willReturnCallback(
            function (MollieExpressCheckoutPaymentUpdateRequestTransfer $requestTransfer) use (&$updates) {
                $updates[] = $requestTransfer;

                // No payment row before the order exists, the row exists afterwards.
                return count($updates) > 1;
            },
        );
        $entityManagerMock->expects($this->once())->method('createExpressCheckoutMolliePayment')->with(138, static::UUID);

        $handler = $this->createHandler($entityManagerMock);

        // 1. webhook arrives first: no row yet -> remembered
        $response = $handler->updateExpressCheckoutMolliePayment($this->createWebhookRequest());
        $this->assertFalse($response->getIsSuccessful());
        $this->assertArrayHasKey(static::STORAGE_KEY, $this->storage);

        // 2. order is created: row created and remembered payment applied, memory cleared
        $handler->createExpressCheckoutMolliePayment($this->createQuote(), $this->createCheckoutResponse());

        $this->assertCount(2, $updates);
        $this->assertSame('tr_riJSrDBms8mKZ5QCrxiXJ', $updates[1]->getTransactionId());
        $this->assertSame('paid', $updates[1]->getStatus());
        $this->assertArrayNotHasKey(static::STORAGE_KEY, $this->storage);
    }

    /**
     * @return void
     */
    public function testWebhookAfterOrderUpdatesRowWithoutRemembering(): void
    {
        $entityManagerMock = $this->createMock(MollieEntityManagerInterface::class);
        $entityManagerMock->method('updateExpressCheckoutMolliePayment')->willReturn(true);

        $response = $this->createHandler($entityManagerMock)->updateExpressCheckoutMolliePayment($this->createWebhookRequest());

        $this->assertTrue($response->getIsSuccessful());
        $this->assertSame([], $this->storage);
    }

    /**
     * @return void
     */
    public function testOrderCreationWithoutRememberedPaymentOnlyCreatesRow(): void
    {
        $entityManagerMock = $this->createMock(MollieEntityManagerInterface::class);
        $entityManagerMock->expects($this->once())->method('createExpressCheckoutMolliePayment');
        $entityManagerMock->expects($this->never())->method('updateExpressCheckoutMolliePayment');

        $this->createHandler($entityManagerMock)->createExpressCheckoutMolliePayment($this->createQuote(), $this->createCheckoutResponse());
    }

    /**
     * @param \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface $entityManager
     *
     * @return \Mollie\Zed\Mollie\Business\Handler\ExpressCheckoutMolliePaymentHandler
     */
    protected function createHandler(MollieEntityManagerInterface $entityManager): ExpressCheckoutMolliePaymentHandler
    {
        $storageClientMock = $this->createMock(MollieToStorageClientInterface::class);
        $storageClientMock->method('set')->willReturnCallback(function (string $key, mixed $value) {
            $this->storage[$key] = json_decode($value, true);

            return true;
        });
        $storageClientMock->method('get')->willReturnCallback(fn (string $key) => $this->storage[$key] ?? null);
        $storageClientMock->method('delete')->willReturnCallback(function (string $key) {
            unset($this->storage[$key]);

            return 1;
        });

        return new ExpressCheckoutMolliePaymentHandler($entityManager, $storageClientMock, new MollieConfig());
    }

    /**
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer
     */
    protected function createWebhookRequest(): MollieExpressCheckoutPaymentUpdateRequestTransfer
    {
        return (new MollieExpressCheckoutPaymentUpdateRequestTransfer())
            ->setExpressCheckoutUuid(static::UUID)
            ->setTransactionId('tr_riJSrDBms8mKZ5QCrxiXJ')
            ->setStatus('paid');
    }

    /**
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function createQuote(): QuoteTransfer
    {
        return (new QuoteTransfer())->setPayment(
            (new PaymentTransfer())->setMollieExpressPayment((new MollieExpressPaymentTransfer())->setExpressCheckoutUuid(static::UUID)),
        );
    }

    /**
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    protected function createCheckoutResponse(): CheckoutResponseTransfer
    {
        return (new CheckoutResponseTransfer())->setSaveOrder((new SaveOrderTransfer())->setIdSalesOrder(138));
    }
}
