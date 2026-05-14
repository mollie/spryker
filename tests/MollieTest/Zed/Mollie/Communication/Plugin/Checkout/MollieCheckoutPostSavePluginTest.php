<?php

declare(strict_types = 1);

namespace MollieTest\Zed\Mollie\Communication\Plugin\Checkout;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Zed\Mollie\Business\MollieFacade;
use Mollie\Zed\Mollie\Communication\Plugin\Checkout\MollieCheckoutPostSavePlugin;
use Mollie\Zed\Mollie\MollieConfig;

class MollieCheckoutPostSavePluginTest extends Unit
{
    /**
     * @return void
     */
    public function testExecuteHookProceedsWithCreatePaymentWhenPaymentProviderIsMollie(): void
    {
        $facadeMock = $this->getMockBuilder(MollieFacade::class)
            ->onlyMethods(['createPayment'])
            ->getMock();
        $facadeMock->expects($this->once())->method('createPayment');

        $plugin = new MollieCheckoutPostSavePlugin();
        $plugin->setFacade($facadeMock);
        $plugin->setConfig(new MollieConfig());

        $plugin->executeHook(
            (new QuoteTransfer())->setPayment((new PaymentTransfer())->setPaymentProvider('MollieCreditCardPayment')),
            new CheckoutResponseTransfer(),
        );
    }

    /**
     * @return void
     */
    public function testExecuteHookSkipsCreatePaymentWhenPaymentProviderIsNotMollie(): void
    {
        $facadeMock = $this->getMockBuilder(MollieFacade::class)
            ->onlyMethods(['createPayment'])
            ->getMock();
        $facadeMock->expects($this->never())->method('createPayment');

        $plugin = new MollieCheckoutPostSavePlugin();
        $plugin->setFacade($facadeMock);
        $plugin->setConfig(new MollieConfig());

        $plugin->executeHook(
            (new QuoteTransfer())->setPayment((new PaymentTransfer())->setPaymentProvider('Paypal')),
            new CheckoutResponseTransfer(),
        );
    }
}