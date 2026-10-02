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
use Mollie\Zed\Mollie\Business\Handler\MolliePaymentHandler;
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
     * @param \Generated\Shared\Transfer\MollieLinksTransfer $mollieLinksTransfer
     *
     * @return \Mollie\Zed\Mollie\Business\Handler\MolliePaymentHandler
     */
    protected function createMolliePaymentHandler(MollieLinksTransfer $mollieLinksTransfer): MolliePaymentHandler
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

        $mollieConfigMock = $this->createMock(MollieConfig::class);
        $mollieConfigMock->method('getMollieRedirectUrl')
            ->willReturn(static::MOLLIE_REDIRECT_URL);

        return new MolliePaymentHandler(
            $mollieClientMock,
            $this->createMock(MollieToStorageClientInterface::class),
            $this->createMock(MolliePaymentWriterInterface::class),
            $mollieConfigMock,
        );
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
