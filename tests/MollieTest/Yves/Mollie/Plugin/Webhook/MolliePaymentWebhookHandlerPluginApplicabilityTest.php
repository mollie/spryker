<?php

declare(strict_types = 1);

namespace MollieTest\Yves\Mollie\Plugin\Webhook;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Mollie\Shared\Mollie\MollieConfig;
use Mollie\Yves\Mollie\Plugin\Webhook\MollieExpressCheckoutPaymentWebhookHandlerPlugin;
use Mollie\Yves\Mollie\Plugin\Webhook\MolliePaymentWebhookHandlerPlugin;

class MolliePaymentWebhookHandlerPluginApplicabilityTest extends Unit
{
    /**
     * @var string
     */
    protected const PAYMENT_ID = 'tr_123123';

    /**
     * @var string
     */
    protected const REFUND_ID = 're_123123';

    /**
     * @var string
     */
    protected const EXPRESS_CHECKOUT_UUID = '2f0b1e8e-6b8a-4c55-9a51-0f3f2a3d9c11';

    /**
     * @return void
     */
    public function testClassicPaymentIsHandledOnlyByClassicPlugin(): void
    {
        $molliePaymentTransfer = new MolliePaymentTransfer();
        $molliePaymentTransfer->setId(static::PAYMENT_ID);
        $molliePaymentTransfer->setMetadata(['orderReference' => 'DE--1']);

        $classicPlugin = new MolliePaymentWebhookHandlerPlugin();
        $expressCheckoutPlugin = new MollieExpressCheckoutPaymentWebhookHandlerPlugin();

        $this->assertTrue($classicPlugin->isApplicable($molliePaymentTransfer));
        $this->assertFalse($expressCheckoutPlugin->isApplicable($molliePaymentTransfer));
    }

    /**
     * @return void
     */
    public function testExpressCheckoutPaymentIsHandledOnlyByExpressCheckoutPlugin(): void
    {
        $molliePaymentTransfer = new MolliePaymentTransfer();
        $molliePaymentTransfer->setId(static::PAYMENT_ID);
        $molliePaymentTransfer->setMetadata([MollieConfig::EXPRESS_CHECKOUT_METADATA_KEY_UUID => static::EXPRESS_CHECKOUT_UUID]);

        $classicPlugin = new MolliePaymentWebhookHandlerPlugin();
        $expressCheckoutPlugin = new MollieExpressCheckoutPaymentWebhookHandlerPlugin();

        $this->assertFalse($classicPlugin->isApplicable($molliePaymentTransfer));
        $this->assertTrue($expressCheckoutPlugin->isApplicable($molliePaymentTransfer));
    }

    /**
     * @return void
     */
    public function testNonPaymentIdIsHandledByNeitherPlugin(): void
    {
        $molliePaymentTransfer = new MolliePaymentTransfer();
        $molliePaymentTransfer->setId(static::REFUND_ID);
        $molliePaymentTransfer->setMetadata([MollieConfig::EXPRESS_CHECKOUT_METADATA_KEY_UUID => static::EXPRESS_CHECKOUT_UUID]);

        $classicPlugin = new MolliePaymentWebhookHandlerPlugin();
        $expressCheckoutPlugin = new MollieExpressCheckoutPaymentWebhookHandlerPlugin();

        $this->assertFalse($classicPlugin->isApplicable($molliePaymentTransfer));
        $this->assertFalse($expressCheckoutPlugin->isApplicable($molliePaymentTransfer));
    }
}
