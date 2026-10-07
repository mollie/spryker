<?php

declare(strict_types = 1);

namespace MollieTest\Zed\Mollie\Communication\Form;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\CurrencyCollectionTransfer;
use Mollie\Zed\Mollie\Communication\Form\CreatePaymentLinkForm;
use Mollie\Zed\Mollie\Communication\Form\DataProvider\PaymentLinkFormDataProvider;
use Mollie\Zed\Mollie\Communication\MollieCommunicationFactory;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToCurrencyFacadeInterface;
use Mollie\Zed\Mollie\MollieConfig;

class PaymentLinkFormDataProviderTest extends Unit
{
    /**
     * @return void
     */
    public function testGetOptionsExcludesBnplPaymentMethodsFromAvailablePaymentMethods(): void
    {
        $paymentMethodMapping = [
            'mollieCreditCardPayment' => 'creditcard',
            'mollieIdealPayment' => 'ideal',
            'mollieKlarnaPayment' => 'klarna',
            'mollieKlarnaPayLaterPayment' => 'klarna',
            'mollieBilliePayment' => 'billie',
            'mollieIdealIn3Payment' => 'in3',
        ];
        $bnplPaymentMethods = ['billie', 'in3', 'klarna', 'riverty', 'voucher', 'alma'];

        $options = $this->createPaymentLinkFormDataProvider($paymentMethodMapping, $bnplPaymentMethods)->getOptions();

        $this->assertSame(
            [
                'mollieCreditCardPayment' => 'creditcard',
                'mollieIdealPayment' => 'ideal',
            ],
            $options[CreatePaymentLinkForm::OPTION_AVAILABLE_PAYMENT_METHODS],
        );
    }

    /**
     * @param array<string, string> $paymentMethodMapping
     * @param array<int, string> $bnplPaymentMethods
     *
     * @return \Mollie\Zed\Mollie\Communication\Form\DataProvider\PaymentLinkFormDataProvider
     */
    protected function createPaymentLinkFormDataProvider(array $paymentMethodMapping, array $bnplPaymentMethods): PaymentLinkFormDataProvider
    {
        $currencyFacadeMock = $this->createMock(MollieToCurrencyFacadeInterface::class);
        $currencyFacadeMock->method('getCurrencyCollection')
            ->willReturn(new CurrencyCollectionTransfer());

        $mollieConfigMock = $this->createMock(MollieConfig::class);
        $mollieConfigMock->method('getMollieOmsToPaymentMethodMapping')
            ->willReturn($paymentMethodMapping);
        $mollieConfigMock->method('getBNPLPaymentMethods')
            ->willReturn($bnplPaymentMethods);

        $mollieCommunicationFactory = $this->getMockBuilder(MollieCommunicationFactory::class)
            ->onlyMethods(['getCurrencyFacade'])
            ->getMock();
        $mollieCommunicationFactory->method('getCurrencyFacade')
            ->willReturn($currencyFacadeMock);
        $mollieCommunicationFactory->setConfig($mollieConfigMock);

        $paymentLinkFormDataProvider = $mollieCommunicationFactory->createMolliePaymentLinkFormDataProvider();

        return $paymentLinkFormDataProvider;
    }
}
