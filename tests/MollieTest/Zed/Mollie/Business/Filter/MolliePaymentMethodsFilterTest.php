<?php

namespace MollieTest\Zed\Mollie\Business\Filter;

use ArrayObject;
use Faker\Factory;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodTransfer;
use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;
use Generated\Shared\Transfer\PaymentProviderTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use MollieTest\Zed\Mollie\Business\AbstractBusinessTest;

class MolliePaymentMethodsFilterTest extends AbstractBusinessTest
{
    /**
     * @var \Faker\Factory
     */
    protected $faker;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();

        $this->mollieClient = $this->createMollieClientMock();
        $this->mollieConfig = $this->createMollieConfigMock(false);
        $this->businessFactory = $this->createMollieBusinessFactory();
        $this->mollieFacade->setFactory($this->businessFactory);
    }

    /**
     * @return void
     */
    public function testFilterActiveMolliePaymentMethodsReturnsFilteredMethods(): void
    {
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
        $paymentMethodsTransfer = new PaymentMethodsTransfer();
        $paymentMethodsTransfer->setMethods($methods);

        $quoteTransfer = $this->createQuoteTransfer(12000, 'DE');

        $filteredTransfer = new PaymentMethodsTransfer();
        $filteredMethods = new ArrayObject([$creditCardPaymentMethod, $payPalPaymentMethod]);
        $filteredTransfer->setMethods($filteredMethods);

        $mollieApiResponse = $this->createMollieApiResponse([
            $this->createMollieMethod('creditcard', 1.00, 10000.00),
            $this->createMollieMethod('paypal', 0.01, 8000.00),
        ]);

        $this->mollieClient
            ->expects($this->once())
            ->method('getEnabledPaymentMethods')
            ->with($this->isInstanceOf(MollieApiRequestTransfer::class))
            ->willReturn($mollieApiResponse);

        $result = $this->mollieFacade->filterActiveMolliePaymentMethods($paymentMethodsTransfer, $quoteTransfer);

        $this->assertCount(2, $result->getMethods());
        $this->assertEquals('mollieCreditCardPayment', $result->getMethods()[0]->getPaymentMethodKey());
        $this->assertEquals('molliePayPalPayment', $result->getMethods()[1]->getPaymentMethodKey());
    }

    /**
     * @return void
     */
    public function testApplyFilterRemovesInactiveMollieMethods(): void
    {
        $paymentMethodsTransfer = new PaymentMethodsTransfer();
        $creditCardMethod = $this->createPaymentMethod('mollieCreditCardPayment', 'MollieCreditCardPayment', 'Mollie Credit Card Payment');
        $payPalMethod = $this->createPaymentMethod('molliePayPalPayment', 'MolliePayPalPayment', 'Mollie PayPal Payment');
        $idealMethod = $this->createPaymentMethod('mollieIdealPayment', 'MollieIdealPayment', 'Mollie iDEAL Payment');

        $paymentMethodsTransfer->setMethods(new ArrayObject([
            $creditCardMethod,
            $payPalMethod,
            $idealMethod,
        ]));

        $quoteTransfer = $this->createQuoteTransfer(10000, 'DE');

        $mollieApiResponse = $this->createMollieApiResponse([
            $this->createMollieMethod('creditcard', 1.00, 10000.00),
            $this->createMollieMethod('paypal', 0.01, 8000.00),
        ]);

        $this->mollieClient
            ->expects($this->once())
            ->method('getEnabledPaymentMethods')
            ->with($this->isInstanceOf(MollieApiRequestTransfer::class))
            ->willReturn($mollieApiResponse);

        $result = $this->mollieFacade->filterActiveMolliePaymentMethods($paymentMethodsTransfer, $quoteTransfer);

        $this->assertCount(2, $result->getMethods());
        $resultKeys = array_map(
            fn ($method) => $method->getPaymentMethodKey(),
            iterator_to_array($result->getMethods()),
        );
        $this->assertContains('mollieCreditCardPayment', $resultKeys);
        $this->assertContains('molliePayPalPayment', $resultKeys);
        $this->assertNotContains('mollieIdealPayment', $resultKeys);
    }

    /**
     * @return void
     */
    public function testApplyFilterRemovesMethodsBelowMinimumAmount(): void
    {
        $paymentMethodsTransfer = new PaymentMethodsTransfer();
        $creditCardMethod = $this->createPaymentMethod('mollieCreditCardPayment', 'MollieCreditCardPayment', 'Mollie Credit Card Payment');
        $paymentMethodsTransfer->setMethods(new ArrayObject([$creditCardMethod]));

        $quoteTransfer = $this->createQuoteTransfer(5000, 'DE');

        $mollieApiResponse = $this->createMollieApiResponse([
            $this->createMollieMethod('creditcard', 100.00, 10000.00),
        ]);

        $this->mollieClient
            ->expects($this->once())
            ->method('getEnabledPaymentMethods')
            ->willReturn($mollieApiResponse);

        $result = $this->mollieFacade->filterActiveMolliePaymentMethods($paymentMethodsTransfer, $quoteTransfer);

        $this->assertCount(0, $result->getMethods());
    }

    /**
     * @return void
     */
    public function testApplyFilterRemovesMethodsAboveMaximumAmount(): void
    {
        $paymentMethodsTransfer = new PaymentMethodsTransfer();
        $idealMethod = $this->createPaymentMethod('mollieIdealPayment', 'MollieIdealPayment', 'Mollie iDEAL Payment');
        $paymentMethodsTransfer->setMethods(new ArrayObject([$idealMethod]));

        $quoteTransfer = $this->createQuoteTransfer(6000000, 'NL');

        $mollieApiResponse = $this->createMollieApiResponse([
            $this->createMollieMethod('ideal', 0.01, 50000.00),
        ]);

        $this->mollieClient
            ->expects($this->once())
            ->method('getEnabledPaymentMethods')
            ->willReturn($mollieApiResponse);

        $result = $this->mollieFacade->filterActiveMolliePaymentMethods($paymentMethodsTransfer, $quoteTransfer);

        $this->assertCount(0, $result->getMethods());
    }

    /**
     * @return void
     */
    public function testApplyFilterKeepsMethodsWithinAmountRange(): void
    {
        $paymentMethodsTransfer = new PaymentMethodsTransfer();
        $creditCardMethod = $this->createPaymentMethod('mollieCreditCardPayment', 'MollieCreditCardPayment', 'Mollie Credit Card Payment');
        $paymentMethodsTransfer->setMethods(new ArrayObject([$creditCardMethod]));

        $quoteTransfer = $this->createQuoteTransfer(15000, 'DE');

        $mollieApiResponse = $this->createMollieApiResponse([
            $this->createMollieMethod('creditcard', 1.00, 10000.00),
        ]);

        $this->mollieClient
            ->expects($this->once())
            ->method('getEnabledPaymentMethods')
            ->willReturn($mollieApiResponse);

        $result = $this->mollieFacade->filterActiveMolliePaymentMethods($paymentMethodsTransfer, $quoteTransfer);

        $this->assertCount(1, $result->getMethods());
        $this->assertEquals('mollieCreditCardPayment', $result->getMethods()[0]->getPaymentMethodKey());
    }

    /**
     * @return void
     */
    public function testApplyFilterKeepsNonMolliePaymentMethods(): void
    {
        $paymentMethodsTransfer = new PaymentMethodsTransfer();
        $mollieMethod = $this->createPaymentMethod('mollieCreditCardPayment', 'MollieCreditCardPayment', 'Mollie Credit Card Payment');
        $sprykerMethod = $this->createPaymentMethod('dummyPayment', 'Spryker', 'Spryker');
        $paypalMethod = $this->createPaymentMethod('paypalExpress', 'PayPal', 'PayPal');

        $paymentMethodsTransfer->setMethods(new ArrayObject([
            $mollieMethod,
            $sprykerMethod,
            $paypalMethod,
        ]));

        $quoteTransfer = $this->createQuoteTransfer(10000, 'DE');

        $mollieApiResponse = $this->createMollieApiResponse([]);

        $this->mollieClient
            ->expects($this->once())
            ->method('getEnabledPaymentMethods')
            ->willReturn($mollieApiResponse);

        $result = $this->mollieFacade->filterActiveMolliePaymentMethods($paymentMethodsTransfer, $quoteTransfer);

        $this->assertCount(2, $result->getMethods());
        $resultKeys = array_map(
            fn ($method) => $method->getPaymentMethodKey(),
            iterator_to_array($result->getMethods()),
        );
        $this->assertContains('dummyPayment', $resultKeys);
        $this->assertContains('paypalExpress', $resultKeys);
        $this->assertNotContains('mollieCreditCardPayment', $resultKeys);
    }

    /**
     * @return void
     */
    public function testApplyFilterHandlesMethodsWithoutAmountLimits(): void
    {
        $paymentMethodsTransfer = new PaymentMethodsTransfer();
        $payPalMethod = $this->createPaymentMethod('molliePayPalPayment', 'MolliePayPalPayment', 'Mollie PayPal Payment');
        $paymentMethodsTransfer->setMethods(new ArrayObject([$payPalMethod]));

        $quoteTransfer = $this->createQuoteTransfer(999999999, 'DE');

        $mollieApiResponse = $this->createMollieApiResponse([
            $this->createMollieMethod('paypal', null, null),
        ]);

        $this->mollieClient
            ->expects($this->once())
            ->method('getEnabledPaymentMethods')
            ->willReturn($mollieApiResponse);

        $result = $this->mollieFacade->filterActiveMolliePaymentMethods($paymentMethodsTransfer, $quoteTransfer);

        $this->assertCount(1, $result->getMethods());
    }

    /**
     * @param int $grandTotal
     * @param string $countryCode
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    private function createQuoteTransfer(int $grandTotal, string $countryCode = 'DE'): QuoteTransfer
    {
        $addressTransfer = (new AddressTransfer())
            ->setIso2Code($countryCode);

        return (new QuoteTransfer())
            ->setTotals(
                (new TotalsTransfer())->setGrandTotal($grandTotal),
            )
            ->setBillingAddress($addressTransfer);
    }

    /**
     * @param string $id
     * @param float|null $minAmount
     * @param float|null $maxAmount
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodTransfer
     */
    private function createMollieMethod(
        string $id,
        ?float $minAmount = null,
        ?float $maxAmount = null,
    ): MolliePaymentMethodTransfer {
        $method = (new MolliePaymentMethodTransfer())->setId($id);

        if ($minAmount !== null) {
            $method->setMinimumAmount($this->createMollieAmountTransfer($minAmount));
        }

        if ($maxAmount !== null) {
            $method->setMaximumAmount($this->createMollieAmountTransfer($maxAmount));
        }

        return $method;
    }

    /**
     * @param float|null $minAmount
     *
     * @return \Generated\Shared\Transfer\MollieAmountTransfer
     */
    private function createMollieAmountTransfer(?float $minAmount = null): MollieAmountTransfer
    {
        return (new MollieAmountTransfer())->setValue((string)$minAmount);
    }

    /**
     * @param array<\Generated\Shared\Transfer\MolliePaymentMethodTransfer> $methods
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer
     */
    private function createMollieApiResponse(array $methods): MolliePaymentMethodsApiResponseTransfer
    {
        $collection = (new MolliePaymentMethodCollectionTransfer())
            ->setMethods(new ArrayObject($methods));

        return (new MolliePaymentMethodsApiResponseTransfer())
            ->setCollection($collection);
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
}
