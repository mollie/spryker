<?php

declare(strict_types = 1);

namespace MollieTest\Zed\Mollie\Business\Handler;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\MollieLinksTransfer;
use Generated\Shared\Transfer\MollieLinkTransfer;
use Generated\Shared\Transfer\MolliePaymentApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Zed\Mollie\Business\Handler\MolliePaymentHandlerInterface;
use Mollie\Zed\Mollie\Business\MollieBusinessFactory;
use Mollie\Zed\Mollie\Business\SingleClick\SingleClickPaymentResolver;
use Mollie\Zed\Mollie\Business\SingleClick\SingleClickPaymentResolverInterface;
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
     * @return void
     */
    public function testCreatePaymentRedirectsToPaymentRedirectPageWhenMollieReturnsNoCheckoutLink(): void
    {
        $molliePaymentHandler = $this->createMolliePaymentHandler(new MollieLinksTransfer());

        $checkoutResponseTransfer = $molliePaymentHandler->createPayment(new QuoteTransfer(), $this->createCheckoutResponseTransfer());

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

        $checkoutResponseTransfer = $molliePaymentHandler->createPayment(new QuoteTransfer(), $this->createCheckoutResponseTransfer());

        $this->assertTrue($checkoutResponseTransfer->getIsSuccess());
        $this->assertSame(static::MOLLIE_CHECKOUT_URL, $checkoutResponseTransfer->getRedirectUrl());
    }

    /**
     * @return void
     */
    public function testCreatePaymentRetriesWithFullCheckoutWhenOneClickPaymentFails(): void
    {
        $molliePaymentApiResponseTransfer = (new MolliePaymentApiResponseTransfer())
            ->setIsSuccessful(true)
            ->setMolliePayment((new MolliePaymentTransfer())->setId(static::PAYMENT_ID)->setLinks(new MollieLinksTransfer()));

        $mollieClientMock = $this->createMock(MollieClientInterface::class);
        $mollieClientMock->expects($this->exactly(2))
            ->method('createPayment')
            ->willReturnOnConsecutiveCalls(
                (new MolliePaymentApiResponseTransfer())->setIsSuccessful(false)->setMessage('The mandate is invalid'),
                $molliePaymentApiResponseTransfer,
            );

        $singleClickPaymentResolverMock = $this->createMock(SingleClickPaymentResolverInterface::class);
        $singleClickPaymentResolverMock->method('resolve')->willReturnArgument(0);
        $singleClickPaymentResolverMock->method('isOneClickPayment')->willReturn(true);
        $singleClickPaymentResolverMock->expects($this->once())
            ->method('applyFallback')
            ->with($this->anything(), static::ORDER_REFERENCE, SingleClickPaymentResolver::REASON_ONE_CLICK_PAYMENT_FAILED)
            ->willReturnArgument(0);

        $molliePaymentHandler = $this->createMolliePaymentHandlerWithMocks($mollieClientMock, $singleClickPaymentResolverMock);

        $checkoutResponseTransfer = $molliePaymentHandler->createPayment(new QuoteTransfer(), $this->createCheckoutResponseTransfer());

        $this->assertTrue($checkoutResponseTransfer->getIsSuccess());
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

        $singleClickPaymentResolverMock = $this->createMock(SingleClickPaymentResolverInterface::class);
        $singleClickPaymentResolverMock->method('resolve')->willReturnArgument(0);

        return $this->createMolliePaymentHandlerWithMocks($mollieClientMock, $singleClickPaymentResolverMock);
    }

    /**
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClientMock
     * @param \Mollie\Zed\Mollie\Business\SingleClick\SingleClickPaymentResolverInterface $singleClickPaymentResolverMock
     *
     * @return \Mollie\Zed\Mollie\Business\Handler\MolliePaymentHandlerInterface
     */
    protected function createMolliePaymentHandlerWithMocks(
        MollieClientInterface $mollieClientMock,
        SingleClickPaymentResolverInterface $singleClickPaymentResolverMock,
    ): MolliePaymentHandlerInterface {
        $mollieConfigMock = $this->createMock(MollieConfig::class);
        $mollieConfigMock->method('getMollieRedirectUrl')
            ->willReturn(static::MOLLIE_REDIRECT_URL);

        $mollieBusinessFactoryMock = $this->getMockBuilder(MollieBusinessFactory::class)
            ->onlyMethods(['getMollieClient', 'getStorageClient', 'createMolliePaymentWriter', 'createSingleClickPaymentResolver'])
            ->getMock();
        $mollieBusinessFactoryMock->method('createSingleClickPaymentResolver')
            ->willReturn($singleClickPaymentResolverMock);
        $mollieBusinessFactoryMock->method('getMollieClient')
            ->willReturn($mollieClientMock);
        $mollieBusinessFactoryMock->method('getStorageClient')
            ->willReturn($this->createMock(MollieToStorageClientInterface::class));
        $mollieBusinessFactoryMock->method('createMolliePaymentWriter')
            ->willReturn($this->createMock(MolliePaymentWriterInterface::class));
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
