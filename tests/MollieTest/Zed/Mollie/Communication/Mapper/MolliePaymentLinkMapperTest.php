<?php

declare(strict_types = 1);

namespace MollieTest\Zed\Mollie\Communication\Mapper;

use Codeception\Test\Unit;
use DateTime;
use Mollie\Zed\Mollie\Communication\Mapper\PaymentLink\MolliePaymentLinkMapperInterface;
use Mollie\Zed\Mollie\Communication\MollieCommunicationFactory;

class MolliePaymentLinkMapperTest extends Unit
{
    /**
     * @var string
     */
    protected const CURRENCY_CODE = 'EUR';

    /**
     * @return void
     */
    public function testMapPaymentLinkFormDataMapsAmountAndLeavesMinimumAmountEmpty(): void
    {
        $formData = $this->createFormData(25.5, null);

        $paymentLinkTransfer = $this->createMolliePaymentLinkMapper()->mapPaymentLinkFormDataToMolliePaymentLinkTransfer($formData);

        $this->assertSame('25.50', $paymentLinkTransfer->getAmount()->getValue());
        $this->assertSame(static::CURRENCY_CODE, $paymentLinkTransfer->getAmount()->getCurrency());
        $this->assertNull($paymentLinkTransfer->getMinimumAmount());
    }

    /**
     * @return void
     */
    public function testMapPaymentLinkFormDataMapsMinimumAmountAndLeavesAmountEmpty(): void
    {
        $formData = $this->createFormData(null, 10.0);

        $paymentLinkTransfer = $this->createMolliePaymentLinkMapper()->mapPaymentLinkFormDataToMolliePaymentLinkTransfer($formData);

        $this->assertSame('10.00', $paymentLinkTransfer->getMinimumAmount()->getValue());
        $this->assertSame(static::CURRENCY_CODE, $paymentLinkTransfer->getMinimumAmount()->getCurrency());
        $this->assertNull($paymentLinkTransfer->getAmount());
    }

    /**
     * @return void
     */
    public function testMapPaymentLinkFormDataFormatsAmountWithoutThousandsSeparator(): void
    {
        $formData = $this->createFormData(1234.5, null);

        $paymentLinkTransfer = $this->createMolliePaymentLinkMapper()->mapPaymentLinkFormDataToMolliePaymentLinkTransfer($formData);

        $this->assertSame('1234.50', $paymentLinkTransfer->getAmount()->getValue());
    }

    /**
     * @param float|null $amount
     * @param float|null $minimumAmount
     *
     * @return array<string, mixed>
     */
    protected function createFormData(?float $amount, ?float $minimumAmount): array
    {
        $formData = [
            'currency' => static::CURRENCY_CODE,
            'amount' => $amount,
            'minimumAmount' => $minimumAmount,
            'description' => 'Deposit',
            'expiryDate' => new DateTime('2026-12-31 00:00:00'),
            'redirectUrl' => null,
            'isReusable' => false,
            'paymentMethods' => [],
        ];

        return $formData;
    }

    /**
     * @return \Mollie\Zed\Mollie\Communication\Mapper\PaymentLink\MolliePaymentLinkMapperInterface
     */
    protected function createMolliePaymentLinkMapper(): MolliePaymentLinkMapperInterface
    {
        $molliePaymentLinkMapper = $this->createMollieCommunicationFactory()->createPaymentLinkMapper();

        return $molliePaymentLinkMapper;
    }

    /**
     * @return \Mollie\Zed\Mollie\Communication\MollieCommunicationFactory
     */
    protected function createMollieCommunicationFactory(): MollieCommunicationFactory
    {
        $mollieCommunicationFactory = new MollieCommunicationFactory();

        return $mollieCommunicationFactory;
    }
}
