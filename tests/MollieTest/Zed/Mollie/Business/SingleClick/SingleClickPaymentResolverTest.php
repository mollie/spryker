<?php

declare(strict_types = 1);

namespace MollieTest\Zed\Mollie\Business\SingleClick;

use ArrayObject;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\MollieCreditCardPaymentTransfer;
use Generated\Shared\Transfer\MollieCustomerTransfer;
use Generated\Shared\Transfer\MollieMandateTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Shared\Mollie\MollieConfig as SharedConfig;
use Mollie\Zed\Mollie\Business\SingleClick\MollieCustomerProviderInterface;
use Mollie\Zed\Mollie\Business\SingleClick\SavedCardReaderInterface;
use Mollie\Zed\Mollie\Business\SingleClick\SingleClickConfigReaderInterface;
use Mollie\Zed\Mollie\Business\SingleClick\SingleClickPaymentResolver;

class SingleClickPaymentResolverTest extends Unit
{
    /**
     * @var string
     */
    protected const ORDER_REFERENCE = 'DE--123';

    /**
     * @var string
     */
    protected const CARD_TOKEN = 'tkn_UqAvArS3gw';

    /**
     * @var string
     */
    protected const MANDATE_ID = 'mdt_pWUnw6pkBN';

    /**
     * @var string
     */
    protected const MOLLIE_CUSTOMER_ID = 'cst_8wmqcHMN4U';

    /**
     * @return void
     */
    public function testFeatureDisabledUsesFullCheckoutAndKeepsCardToken(): void
    {
        $quoteTransfer = $this->createQuoteTransfer((new MollieCreditCardPaymentTransfer())
            ->setCardToken(static::CARD_TOKEN)
            ->setIsSaveCardConsentGiven(true));

        $resolver = $this->createResolver(false, $this->createNeverCalledCustomerProvider());
        $creditCardPaymentTransfer = $this->resolve($resolver, $quoteTransfer);

        $this->assertSame(SharedConfig::SINGLE_CLICK_CHECKOUT_PATH_FULL, $creditCardPaymentTransfer->getCheckoutPath());
        $this->assertSame(static::CARD_TOKEN, $creditCardPaymentTransfer->getCardToken());
        $this->assertNull($creditCardPaymentTransfer->getMollieCustomerId());
        $this->assertFalse($creditCardPaymentTransfer->getIsStoreCredentials());
    }

    /**
     * @return void
     */
    public function testFeatureDisabledWithSavedCardFallsBackToMolliePaymentPage(): void
    {
        $quoteTransfer = $this->createQuoteTransfer((new MollieCreditCardPaymentTransfer())->setMandateId(static::MANDATE_ID));

        $resolver = $this->createResolver(false, $this->createNeverCalledCustomerProvider());
        $creditCardPaymentTransfer = $this->resolve($resolver, $quoteTransfer);

        $this->assertSame(SharedConfig::SINGLE_CLICK_CHECKOUT_PATH_FULL_FALLBACK, $creditCardPaymentTransfer->getCheckoutPath());
        $this->assertNull($creditCardPaymentTransfer->getMandateId());
        $this->assertNull($creditCardPaymentTransfer->getCardToken());
    }

    /**
     * @return void
     */
    public function testGuestUsesFullCheckoutEvenWithConsent(): void
    {
        $quoteTransfer = $this->createQuoteTransfer(
            (new MollieCreditCardPaymentTransfer())->setCardToken(static::CARD_TOKEN)->setIsSaveCardConsentGiven(true),
            (new CustomerTransfer())->setIsGuest(true),
        );

        $resolver = $this->createResolver(true, $this->createNeverCalledCustomerProvider());
        $creditCardPaymentTransfer = $this->resolve($resolver, $quoteTransfer);

        $this->assertSame(SharedConfig::SINGLE_CLICK_CHECKOUT_PATH_FULL, $creditCardPaymentTransfer->getCheckoutPath());
        $this->assertNull($creditCardPaymentTransfer->getMollieCustomerId());
    }

    /**
     * @return void
     */
    public function testNoConsentDoesNotCreateMollieCustomer(): void
    {
        $quoteTransfer = $this->createQuoteTransfer((new MollieCreditCardPaymentTransfer())->setCardToken(static::CARD_TOKEN));

        $resolver = $this->createResolver(true, $this->createNeverCalledCustomerProvider());
        $creditCardPaymentTransfer = $this->resolve($resolver, $quoteTransfer);

        $this->assertSame(SharedConfig::SINGLE_CLICK_CHECKOUT_PATH_FULL, $creditCardPaymentTransfer->getCheckoutPath());
        $this->assertNull($creditCardPaymentTransfer->getMollieCustomerId());
        $this->assertSame(static::CARD_TOKEN, $creditCardPaymentTransfer->getCardToken());
    }

    /**
     * @return void
     */
    public function testConsentSavesCardWithMollieCustomer(): void
    {
        $quoteTransfer = $this->createQuoteTransfer((new MollieCreditCardPaymentTransfer())
            ->setCardToken(static::CARD_TOKEN)
            ->setIsSaveCardConsentGiven(true));

        $customerProviderMock = $this->createMock(MollieCustomerProviderInterface::class);
        $customerProviderMock->expects($this->once())
            ->method('getOrCreateMollieCustomer')
            ->willReturn($this->createMollieCustomerTransfer()->setIsNew(true));

        $creditCardPaymentTransfer = $this->resolve($this->createResolver(true, $customerProviderMock), $quoteTransfer);

        $this->assertSame(SharedConfig::SINGLE_CLICK_CHECKOUT_PATH_SAVE_CARD, $creditCardPaymentTransfer->getCheckoutPath());
        $this->assertSame(static::MOLLIE_CUSTOMER_ID, $creditCardPaymentTransfer->getMollieCustomerId());
        $this->assertTrue($creditCardPaymentTransfer->getIsStoreCredentials());
        $this->assertSame(static::CARD_TOKEN, $creditCardPaymentTransfer->getCardToken());
    }

    /**
     * @return void
     */
    public function testConsentFallsBackToFullCheckoutWhenMollieCustomerCannotBeCreated(): void
    {
        $quoteTransfer = $this->createQuoteTransfer((new MollieCreditCardPaymentTransfer())
            ->setCardToken(static::CARD_TOKEN)
            ->setIsSaveCardConsentGiven(true));

        $customerProviderMock = $this->createMock(MollieCustomerProviderInterface::class);
        $customerProviderMock->method('getOrCreateMollieCustomer')->willReturn(null);

        $creditCardPaymentTransfer = $this->resolve($this->createResolver(true, $customerProviderMock), $quoteTransfer);

        $this->assertSame(SharedConfig::SINGLE_CLICK_CHECKOUT_PATH_FULL, $creditCardPaymentTransfer->getCheckoutPath());
        $this->assertSame(static::CARD_TOKEN, $creditCardPaymentTransfer->getCardToken());
        $this->assertNull($creditCardPaymentTransfer->getMollieCustomerId());
    }

    /**
     * @return void
     */
    public function testValidSavedCardUsesOneClickPayment(): void
    {
        $quoteTransfer = $this->createQuoteTransfer((new MollieCreditCardPaymentTransfer())->setMandateId(static::MANDATE_ID));

        $resolver = $this->createResolver(true, $this->createCustomerProviderWithStoredCustomer(), [static::MANDATE_ID]);
        $creditCardPaymentTransfer = $this->resolve($resolver, $quoteTransfer);

        $this->assertSame(SharedConfig::SINGLE_CLICK_CHECKOUT_PATH_ONE_CLICK, $creditCardPaymentTransfer->getCheckoutPath());
        $this->assertSame(static::MOLLIE_CUSTOMER_ID, $creditCardPaymentTransfer->getMollieCustomerId());
        $this->assertSame(static::MANDATE_ID, $creditCardPaymentTransfer->getMandateId());
        $this->assertFalse($creditCardPaymentTransfer->getIsStoreCredentials());
        $this->assertTrue($resolver->isOneClickPayment($quoteTransfer));
    }

    /**
     * @return void
     */
    public function testSavedCardOfAnotherCustomerFallsBackToMolliePaymentPage(): void
    {
        $quoteTransfer = $this->createQuoteTransfer((new MollieCreditCardPaymentTransfer())->setMandateId('mdt_of_someone_else'));

        $resolver = $this->createResolver(true, $this->createCustomerProviderWithStoredCustomer(), [static::MANDATE_ID]);
        $creditCardPaymentTransfer = $this->resolve($resolver, $quoteTransfer);

        $this->assertSame(SharedConfig::SINGLE_CLICK_CHECKOUT_PATH_FULL_FALLBACK, $creditCardPaymentTransfer->getCheckoutPath());
        $this->assertNull($creditCardPaymentTransfer->getMandateId());
        $this->assertNull($creditCardPaymentTransfer->getMollieCustomerId());
    }

    /**
     * @return void
     */
    public function testSavedCardWithoutStoredMollieCustomerFallsBackToMolliePaymentPage(): void
    {
        $quoteTransfer = $this->createQuoteTransfer((new MollieCreditCardPaymentTransfer())->setMandateId(static::MANDATE_ID));

        $customerProviderMock = $this->createMock(MollieCustomerProviderInterface::class);
        $customerProviderMock->method('findMollieCustomer')->willReturn(null);

        $creditCardPaymentTransfer = $this->resolve($this->createResolver(true, $customerProviderMock), $quoteTransfer);

        $this->assertSame(SharedConfig::SINGLE_CLICK_CHECKOUT_PATH_FULL_FALLBACK, $creditCardPaymentTransfer->getCheckoutPath());
    }

    /**
     * @return void
     */
    public function testOtherPaymentMethodsAreNotChanged(): void
    {
        $quoteTransfer = $this->createQuoteTransfer(new MollieCreditCardPaymentTransfer());
        $quoteTransfer->getPayment()->setPaymentMethod(SharedConfig::MOLLIE_PAYMENT_PAYPAL);

        $resolver = $this->createResolver(true, $this->createNeverCalledCustomerProvider());
        $resolver->resolve($quoteTransfer, static::ORDER_REFERENCE);

        $this->assertNull($quoteTransfer->getPayment()->getMollieCreditCardPayment()->getCheckoutPath());
    }

    /**
     * @param \Mollie\Zed\Mollie\Business\SingleClick\SingleClickPaymentResolver $resolver
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\MollieCreditCardPaymentTransfer
     */
    protected function resolve(SingleClickPaymentResolver $resolver, QuoteTransfer $quoteTransfer): MollieCreditCardPaymentTransfer
    {
        return $resolver->resolve($quoteTransfer, static::ORDER_REFERENCE)->getPayment()->getMollieCreditCardPayment();
    }

    /**
     * @param bool $isEnabled
     * @param \Mollie\Zed\Mollie\Business\SingleClick\MollieCustomerProviderInterface $mollieCustomerProvider
     * @param list<string> $savedMandateIds
     *
     * @return \Mollie\Zed\Mollie\Business\SingleClick\SingleClickPaymentResolver
     */
    protected function createResolver(
        bool $isEnabled,
        MollieCustomerProviderInterface $mollieCustomerProvider,
        array $savedMandateIds = [],
    ): SingleClickPaymentResolver {
        $configReaderMock = $this->createMock(SingleClickConfigReaderInterface::class);
        $configReaderMock->method('isSingleClickPaymentEnabled')->willReturn($isEnabled);

        $savedCards = new ArrayObject();
        foreach ($savedMandateIds as $savedMandateId) {
            $savedCards->append((new MollieMandateTransfer())->setId($savedMandateId));
        }
        $savedCardReaderMock = $this->createMock(SavedCardReaderInterface::class);
        $savedCardReaderMock->method('getSavedCards')->willReturn($savedCards);

        return new SingleClickPaymentResolver($configReaderMock, $mollieCustomerProvider, $savedCardReaderMock);
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\SingleClick\MollieCustomerProviderInterface
     */
    protected function createNeverCalledCustomerProvider(): MollieCustomerProviderInterface
    {
        $customerProviderMock = $this->createMock(MollieCustomerProviderInterface::class);
        $customerProviderMock->expects($this->never())->method('getOrCreateMollieCustomer');
        $customerProviderMock->expects($this->never())->method('findMollieCustomer');

        return $customerProviderMock;
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\SingleClick\MollieCustomerProviderInterface
     */
    protected function createCustomerProviderWithStoredCustomer(): MollieCustomerProviderInterface
    {
        $customerProviderMock = $this->createMock(MollieCustomerProviderInterface::class);
        $customerProviderMock->method('findMollieCustomer')->willReturn($this->createMollieCustomerTransfer());

        return $customerProviderMock;
    }

    /**
     * @return \Generated\Shared\Transfer\MollieCustomerTransfer
     */
    protected function createMollieCustomerTransfer(): MollieCustomerTransfer
    {
        return (new MollieCustomerTransfer())
            ->setIdMollieCustomer(1)
            ->setMollieCustomerId(static::MOLLIE_CUSTOMER_ID)
            ->setFkCustomer(42)
            ->setMode('test');
    }

    /**
     * @param \Generated\Shared\Transfer\MollieCreditCardPaymentTransfer $mollieCreditCardPaymentTransfer
     * @param \Generated\Shared\Transfer\CustomerTransfer|null $customerTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function createQuoteTransfer(
        MollieCreditCardPaymentTransfer $mollieCreditCardPaymentTransfer,
        ?CustomerTransfer $customerTransfer = null,
    ): QuoteTransfer {
        $paymentTransfer = (new PaymentTransfer())
            ->setPaymentMethod(SharedConfig::MOLLIE_PAYMENT_CREDIT_CARD)
            ->setMollieCreditCardPayment($mollieCreditCardPaymentTransfer);

        return (new QuoteTransfer())
            ->setPayment($paymentTransfer)
            ->setCustomer($customerTransfer ?? (new CustomerTransfer())->setIdCustomer(42)->setIsGuest(false));
    }
}
