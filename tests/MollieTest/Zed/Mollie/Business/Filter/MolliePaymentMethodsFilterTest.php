<?php

namespace MollieTest\Zed\Mollie\Business\Filter;

use ArrayObject;
use Faker\Factory;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodTransfer;
use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;
use Generated\Shared\Transfer\PaymentProviderTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use Mollie\Service\Mollie\MollieServiceInterface;
use Mollie\Zed\Mollie\Business\Filter\MolliePaymentMethodsFilter;
use Mollie\Zed\Mollie\Business\Filter\MolliePaymentMethodsFilterInterface;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToLocaleFacadeInterface;
use Mollie\Zed\Mollie\MollieConfig;
use MollieTest\Zed\Mollie\Business\AbstractBusinessTest;
use PHPUnit\Framework\MockObject\MockObject;

class MolliePaymentMethodsFilterTest extends AbstractBusinessTest
{
    protected MockObject $filterMock;

    protected MockObject $mollieServiceMock;

    protected MockObject $localeFacadeMock;

    protected MockObject $mollieConfigMock;

    /**
     * @var \Faker\Factory
     */
    protected $faker;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->faker = Factory::create();
        $this->filterMock = $this->createMock(MolliePaymentMethodsFilterInterface::class);
        $this->mollieServiceMock = $this->createMock(MollieServiceInterface::class);
        $this->localeFacadeMock = $this->createMock(MollieToLocaleFacadeInterface::class);
        $this->mollieConfigMock = $this->createMock(MollieConfig::class);
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

        $this->businessFactory
            ->expects($this->once())
            ->method('createMolliePaymentMethodsFilter')
            ->willReturn($this->filterMock);

        $this->filterMock
            ->expects($this->once())
            ->method('applyFilter')
            ->with($paymentMethodsTransfer, $quoteTransfer)
            ->willReturn($filteredTransfer);

        $result = $this->mollieFacade->filterActiveMolliePaymentMethods($paymentMethodsTransfer, $quoteTransfer);

        $this->assertSame($filteredTransfer, $result);
        $this->assertCount(1, $result->getMethods());
        $this->assertEquals('molliePayPalPayment', $result->getMethods()[0]->getPaymentMethodKey());
    }

    /**
     * @return void
     */
    public function testApplyFilterRemovesInactiveMollieMethods(): void
    {
        $filter = $this->createFilterInstance();

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

        $this->setupMollieConfigMapping([
            'mollieCreditCardPayment' => 'creditcard',
            'molliePayPalPayment' => 'paypal',
            'mollieIdealPayment' => 'ideal',
        ]);

        $this->mollieServiceMock
            ->method('convertIntegerToDecimal')
            ->willReturnCallback(fn ($amount) => $amount / 100);

        $result = $filter->applyFilter($paymentMethodsTransfer, $quoteTransfer);

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
        $filter = $this->createFilterInstance();

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

        $this->setupMollieConfigMapping(['mollieCreditCardPayment' => 'creditcard']);

        $this->mollieServiceMock
            ->method('convertIntegerToDecimal')
            ->willReturn(50.00);

        $result = $filter->applyFilter($paymentMethodsTransfer, $quoteTransfer);

        $this->assertCount(0, $result->getMethods());
    }

    /**
     * @return void
     */
    public function testApplyFilterRemovesMethodsAboveMaximumAmount(): void
    {
        $filter = $this->createFilterInstance();

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

        $this->setupMollieConfigMapping(['mollieIdealPayment' => 'ideal']);

        $this->mollieServiceMock
            ->method('convertIntegerToDecimal')
            ->willReturn(60000.00);

        $result = $filter->applyFilter($paymentMethodsTransfer, $quoteTransfer);

        $this->assertCount(0, $result->getMethods());
    }

    /**
     * @return void
     */
    public function testApplyFilterKeepsMethodsWithinAmountRange(): void
    {
        $filter = $this->createFilterInstance();

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

        $this->setupMollieConfigMapping(['mollieCreditCardPayment' => 'creditcard']);

        $this->mollieServiceMock
            ->method('convertIntegerToDecimal')
            ->willReturn(150.00);

        $result = $filter->applyFilter($paymentMethodsTransfer, $quoteTransfer);

        $this->assertCount(1, $result->getMethods());
        $this->assertEquals('mollieCreditCardPayment', $result->getMethods()[0]->getPaymentMethodKey());
    }

    /**
     * @return void
     */
    public function testApplyFilterKeepsNonMolliePaymentMethods(): void
    {
        $filter = $this->createFilterInstance();

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

        $this->setupMollieConfigMapping(['mollieCreditCardPayment' => 'creditcard']);

        $this->mollieServiceMock
            ->method('convertIntegerToDecimal')
            ->willReturn(100.00);

        $result = $filter->applyFilter($paymentMethodsTransfer, $quoteTransfer);

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
        $filter = $this->createFilterInstance();

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

        $this->setupMollieConfigMapping(['molliePayPalPayment' => 'paypal']);

        $this->mollieServiceMock
            ->method('convertIntegerToDecimal')
            ->willReturn(9999999.99);

        $result = $filter->applyFilter($paymentMethodsTransfer, $quoteTransfer);

        $this->assertCount(1, $result->getMethods());
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\Filter\MolliePaymentMethodsFilter
     */
    private function createFilterInstance(): MolliePaymentMethodsFilter
    {
        $localeMock = new LocaleTransfer();
        $localeMock->setLocaleName('de_DE');

        $this->localeFacadeMock
            ->method('getCurrentLocale')
            ->willReturn($localeMock);

        return new MolliePaymentMethodsFilter(
            $this->mollieClient,
            $this->mollieServiceMock,
            $this->localeFacadeMock,
            $this->mollieConfigMock,
        );
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
            $method->setMinimumAmount([
                MollieConfig::MOLLIE_PAYMENT_METHOD_AMOUNT_VALUE => (string)$minAmount,
            ]);
        }

        if ($maxAmount !== null) {
            $method->setMaximumAmount([
                MollieConfig::MOLLIE_PAYMENT_METHOD_AMOUNT_VALUE => (string)$maxAmount,
            ]);
        }

        return $method;
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
     * @param array<string, string> $mapping
     *
     * @return void
     */
    private function setupMollieConfigMapping(array $mapping): void
    {
        $this->mollieConfigMock
            ->method('getMolliePaymentMethod')
            ->willReturnCallback(function ($key) use ($mapping) {
                return $mapping[$key] ?? null;
            });
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
