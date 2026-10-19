<?php

declare(strict_types = 1);

namespace MollieTest\Zed\Mollie\Business\Handler;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\MollieCreditCardPaymentTransfer;
use Generated\Shared\Transfer\MollieLinksTransfer;
use Generated\Shared\Transfer\MollieLinkTransfer;
use Generated\Shared\Transfer\MolliePaymentApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Shared\Mollie\MollieConfig as SharedConfig;
use Mollie\Zed\Mollie\Business\Handler\MolliePaymentHandlerInterface;
use Mollie\Zed\Mollie\Business\MollieBusinessFactory;
use Mollie\Zed\Mollie\Business\Writer\MolliePaymentWriterInterface;
use Mollie\Zed\Mollie\Dependency\MollieToStorageClientInterface;
use Mollie\Zed\Mollie\MollieConfig;

class MolliePaymentHandlerTest extends Unit
{
    /**
     * @var string
     */
    protected const ORDER_REFERENCE = 'DE--123';

    /**
     * @var string
     */
    protected const PAYMENT_ID = 'tr_123123';

    /**
     * @var string
     */
    protected const MOLLIE_REDIRECT_URL = 'https://shop.example.com/checkout/payment-redirect';

    /**
     * @var string
     */
    protected const MOLLIE_CHECKOUT_URL = 'https://www.mollie.com/checkout/select-method/123123';

    /**
     * @var string
     */
    protected const MANDATE_ID = 'mdt_123123';

    /**
     * @return void
     */
    public function testCreatePaymentRedirectsToPaymentRedirectPageWhenMollieReturnsNoCheckoutLink(): void
    {
        $molliePaymentHandler = $this->createMolliePaymentHandler(new MollieLinksTransfer());

        $checkoutResponseTransfer = $molliePaymentHandler->createPayment((new QuoteTransfer())->setPayment(new PaymentTransfer()), $this->createCheckoutResponseTransfer());

        $this->assertTrue($checkoutResponseTransfer->getIsSuccess());
        $this->assertTrue($checkoutResponseTransfer->getIsExternalRedirect());
        $this->assertSame(static::MOLLIE_REDIRECT_URL . '?orderReference=' . static::ORDER_REFERENCE, $checkoutResponseTransfer->getRedirectUrl());
    }

    /**
     * @return void
     */
    public function testCreatePaymentRedirectsToMollieCheckoutWhenMollieReturnsCheckoutLink(): void
    {
        $mollieLinksTransfer = (new MollieLinksTransfer())
            ->setCheckout((new MollieLinkTransfer())->setHref(static::MOLLIE_CHECKOUT_URL));
        $molliePaymentHandler = $this->createMolliePaymentHandler($mollieLinksTransfer);

        $checkoutResponseTransfer = $molliePaymentHandler->createPayment((new QuoteTransfer())->setPayment(new PaymentTransfer()), $this->createCheckoutResponseTransfer());

        $this->assertTrue($checkoutResponseTransfer->getIsSuccess());
        $this->assertSame(static::MOLLIE_CHECKOUT_URL, $checkoutResponseTransfer->getRedirectUrl());
    }

    /**
     * @return void
     */
    public function testCreatePaymentStoresRequestedMandateIdWhenMollieReturnsNone(): void
    {
        $molliePaymentApiResponseTransfer = (new MolliePaymentApiResponseTransfer())
            ->setIsSuccessful(true)
            ->setMolliePayment((new MolliePaymentTransfer())->setId(static::PAYMENT_ID)->setLinks(new MollieLinksTransfer()));

        $mollieClientMock = $this->createMock(MollieClientInterface::class);
        $mollieClientMock->method('createPayment')->willReturn($molliePaymentApiResponseTransfer);

        $molliePaymentWriterMock = $this->createMock(MolliePaymentWriterInterface::class);
        $molliePaymentWriterMock->expects($this->once())
            ->method('addMolliePaymentData')
            ->with(1, $this->callback(function (MolliePaymentTransfer $molliePaymentTransfer): bool {
                return $molliePaymentTransfer->getMandateId() === static::MANDATE_ID;
            }));

        $molliePaymentHandler = $this->createMolliePaymentHandlerWithMocks($mollieClientMock, $molliePaymentWriterMock);
        $quoteTransfer = (new QuoteTransfer())->setPayment(
            (new PaymentTransfer())
                ->setPaymentMethod(SharedConfig::MOLLIE_PAYMENT_CREDIT_CARD)
                ->setMollieCreditCardPayment((new MollieCreditCardPaymentTransfer())->setMandateId(static::MANDATE_ID)),
        );

        $molliePaymentHandler->createPayment($quoteTransfer, $this->createCheckoutResponseTransfer());
    }

    /**
     * @param \Generated\Shared\Transfer\MollieLinksTransfer $mollieLinksTransfer
     *
     * @return \Mollie\Zed\Mollie\Business\Handler\MolliePaymentHandlerInterface
     */
    protected function createMolliePaymentHandler(MollieLinksTransfer $mollieLinksTransfer): MolliePaymentHandlerInterface
    {
        $molliePaymentTransfer = (new MolliePaymentTransfer())
            ->setId(static::PAYMENT_ID)
            ->setLinks($mollieLinksTransfer);
        $molliePaymentApiResponseTransfer = (new MolliePaymentApiResponseTransfer())
            ->setIsSuccessful(true)
            ->setMolliePayment($molliePaymentTransfer);

        $mollieClientMock = $this->createMock(MollieClientInterface::class);
        $mollieClientMock->method('createPayment')
            ->willReturn($molliePaymentApiResponseTransfer);

        return $this->createMolliePaymentHandlerWithMocks($mollieClientMock);
    }

    /**
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClientMock
     * @param \Mollie\Zed\Mollie\Business\Writer\MolliePaymentWriterInterface|null $molliePaymentWriterMock
     *
     * @return \Mollie\Zed\Mollie\Business\Handler\MolliePaymentHandlerInterface
     */
    protected function createMolliePaymentHandlerWithMocks(
        MollieClientInterface $mollieClientMock,
        ?MolliePaymentWriterInterface $molliePaymentWriterMock = null,
    ): MolliePaymentHandlerInterface {
        $mollieConfigMock = $this->createMock(MollieConfig::class);
        $mollieConfigMock->method('getMollieRedirectUrl')
            ->willReturn(static::MOLLIE_REDIRECT_URL);

        $mollieBusinessFactoryMock = $this->getMockBuilder(MollieBusinessFactory::class)
            ->onlyMethods(['getMollieClient', 'getStorageClient', 'createMolliePaymentWriter'])
            ->getMock();
        $mollieBusinessFactoryMock->method('getMollieClient')
            ->willReturn($mollieClientMock);
        $mollieBusinessFactoryMock->method('getStorageClient')
            ->willReturn($this->createMock(MollieToStorageClientInterface::class));
        $mollieBusinessFactoryMock->method('createMolliePaymentWriter')
            ->willReturn($molliePaymentWriterMock ?? $this->createMock(MolliePaymentWriterInterface::class));
        $mollieBusinessFactoryMock->setConfig($mollieConfigMock);

        return $mollieBusinessFactoryMock->createMolliePaymentHandler();
    }

    /**
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    protected function createCheckoutResponseTransfer(): CheckoutResponseTransfer
    {
        $saveOrderTransfer = (new SaveOrderTransfer())
            ->setOrderReference(static::ORDER_REFERENCE)
            ->setIdSalesOrder(1);

        return (new CheckoutResponseTransfer())->setSaveOrder($saveOrderTransfer);
    }
}
