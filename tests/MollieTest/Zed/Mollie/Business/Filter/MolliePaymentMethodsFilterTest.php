<?php

namespace MollieTest\Zed\Mollie\Business\Filter;

use ArrayObject;
use Codeception\Test\Unit;
use Faker\Factory;
use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;
use Generated\Shared\Transfer\PaymentProviderTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use Mollie\Zed\Mollie\Business\Filter\MolliePaymentMethodsFilterInterface;
use Mollie\Zed\Mollie\Business\MollieBusinessFactory;
use Mollie\Zed\Mollie\Business\MollieFacade;
use PHPUnit\Framework\MockObject\MockObject;

class MolliePaymentMethodsFilterTest extends Unit
{
    protected MollieFacade $facade;

    protected MockObject $factoryMock;

    protected MockObject $filterMock;

    /**
     * @var \Faker\Factory
     */
    protected $faker;

    /**
     * @return void
     */
    protected function _before(): void
    {
        $this->faker = Factory::create();

        $this->facade = new MollieFacade();

        $this->factoryMock = $this->createMock(MollieBusinessFactory::class);
        $this->filterMock = $this->createMock(MolliePaymentMethodsFilterInterface::class);

        $this->facade->setFactory($this->factoryMock);
    }

    /**
     * @return void
     */
    public function testFilterActiveMolliePaymentMethodsReturnsFilteredMethods(): void
    {
        $paymentMethodsTransfer = new PaymentMethodsTransfer();

        $creditCardPaymentMethod = $this->createPaymentMethod(
            'mollieCreditCardPayment',
            'MollieCreditCardPayment',
            'Mollie Credit Card Payment',
        );

        $payPalPaymentMethod = $this->createPaymentMethod(
            'molliePayPalPayment',
            'MolliePayPalPayment',
            'Mollie PayPal Payment',
        );

        $methods = new ArrayObject([$creditCardPaymentMethod, $payPalPaymentMethod]);
        $paymentMethodsTransfer->setMethods($methods);

        $quoteTransfer = $this->createQuoteTransferWithGrandTotal(12000);

        $filteredTransfer = new PaymentMethodsTransfer();
        $filteredMethods = new ArrayObject([$payPalPaymentMethod]);
        $filteredTransfer->setMethods($filteredMethods);

        $this->factoryMock
            ->expects($this->once())
            ->method('createMolliePaymentMethodsFilter')
            ->willReturn($this->filterMock);

        $this->filterMock
            ->expects($this->once())
            ->method('applyFilter')
            ->with($paymentMethodsTransfer, $quoteTransfer)
            ->willReturn($filteredTransfer);

        $result = $this->facade->filterActiveMolliePaymentMethods($paymentMethodsTransfer, $quoteTransfer);

        $this->assertSame($filteredTransfer, $result);
        $this->assertCount(1, $result->getMethods());
        $this->assertEquals('molliePayPalPayment', $result->getMethods()[0]->getPaymentMethodKey());
    }

    /**
     * @param string $methodKey
     * @param string $providerKey
     * @param string $providerName
     *
     * @return \Generated\Shared\Transfer\PaymentMethodTransfer
     */
    private function createPaymentMethod(
        string $methodKey,
        string $providerKey,
        string $providerName,
    ): PaymentMethodTransfer {
        $providerTransfer = (new PaymentProviderTransfer())
            ->setName($providerName)
            ->setPaymentProviderKey($providerKey);

        return (new PaymentMethodTransfer())
            ->setPaymentMethodKey($methodKey)
            ->setPaymentProvider($providerTransfer);
    }

    /**
     * @param int $grandTotal
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    private function createQuoteTransferWithGrandTotal(int $grandTotal): QuoteTransfer
    {
        return (new QuoteTransfer())
            ->setTotals(
                (new TotalsTransfer())->setGrandTotal($grandTotal),
            );
    }
}
