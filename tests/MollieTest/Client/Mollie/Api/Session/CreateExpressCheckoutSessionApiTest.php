<?php

declare(strict_types = 1);

namespace MollieTest\Client\Mollie\Api\Session;

use ArrayObject;
use Generated\Shared\Transfer\CurrencyTransfer;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\CreateSessionRequest;
use Mollie\Client\Mollie\MollieClientInterface;
use MollieTest\Client\Mollie\AbstractClientTest;
use Spryker\Shared\Shipment\ShipmentConfig;

class CreateExpressCheckoutSessionApiTest extends AbstractClientTest
{
    /**
     * @var string
     */
    protected const CURRENCY_CODE = 'EUR';

    /**
     * @var int
     */
    protected const ITEMS_TOTAL = 2900;

    /**
     * @var int
     */
    protected const SAVED_SHIPPING_COST = 590;

    /**
     * @var array<string, mixed>
     */
    protected const SESSION_RESPONSE = [
        'resource' => 'session',
        'id' => 'sess_test123',
        'status' => 'open',
        'clientAccessToken' => 'client_access_token',
    ];

    /**
     * @return void
     */
    public function testCreateSessionSendsOnlyProductLinesWhenShippingIsSavedOnTheCart(): void
    {
        $mockMollieClient = $this->createMockApiClient([
            CreateSessionRequest::class => new MockResponse(static::SESSION_RESPONSE, 201),
        ]);
        $client = $this->createClient($mockMollieClient);

        $quoteTransfer = $this->createQuoteTransfer();
        $quoteTransfer
            ->addExpense($this->createShippingExpenseTransfer())
            ->setTotals((new TotalsTransfer())->setGrandTotal(static::ITEMS_TOTAL + static::SAVED_SHIPPING_COST));

        $client->createExpressCheckoutSession($this->createMollieApiRequestTransfer($quoteTransfer));

        $mockMollieClient->assertSent(function (PendingRequest $pendingRequest): bool {
            $requestBody = $pendingRequest->getRequest()->payload()->all();
            $lines = $requestBody['lines'];

            return count($lines) === 1
                && $lines[0]['type'] === 'physical'
                && $requestBody['amount']['value'] === '29.00'
                && count($requestBody['shipping']['options']) === 1;
        });
    }

    /**
     * @return void
     */
    public function testCreateSessionSendsGrandTotalWhenNoShippingIsSavedOnTheCart(): void
    {
        $mockMollieClient = $this->createMockApiClient([
            CreateSessionRequest::class => new MockResponse(static::SESSION_RESPONSE, 201),
        ]);
        $client = $this->createClient($mockMollieClient);

        $quoteTransfer = $this->createQuoteTransfer();
        $quoteTransfer->setTotals((new TotalsTransfer())->setGrandTotal(static::ITEMS_TOTAL));

        $client->createExpressCheckoutSession($this->createMollieApiRequestTransfer($quoteTransfer));

        $mockMollieClient->assertSent(function (PendingRequest $pendingRequest): bool {
            $requestBody = $pendingRequest->getRequest()->payload()->all();
            $lines = $requestBody['lines'];

            return count($lines) === 1
                && $requestBody['amount']['value'] === '29.00';
        });
    }

    /**
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function createQuoteTransfer(): QuoteTransfer
    {
        $itemTransfer = new ItemTransfer();
        $itemTransfer
            ->setSku('420692')
            ->setName('Adhesive stripes')
            ->setQuantity(5)
            ->setUnitPrice(580)
            ->setSumPriceToPayAggregation(static::ITEMS_TOTAL)
            ->setSumDiscountAmountAggregation(0)
            ->setSumTaxAmountFullAggregation(463)
            ->setTaxRate(19.0);

        $quoteTransfer = new QuoteTransfer();
        $quoteTransfer
            ->setCurrency((new CurrencyTransfer())->setCode(static::CURRENCY_CODE))
            ->setItems(new ArrayObject([$itemTransfer]));

        return $quoteTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\ExpenseTransfer
     */
    protected function createShippingExpenseTransfer(): ExpenseTransfer
    {
        $expenseTransfer = new ExpenseTransfer();
        $expenseTransfer
            ->setType(ShipmentConfig::SHIPMENT_EXPENSE_TYPE)
            ->setSumPriceToPayAggregation(static::SAVED_SHIPPING_COST);

        return $expenseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\MollieApiRequestTransfer
     */
    protected function createMollieApiRequestTransfer(QuoteTransfer $quoteTransfer): MollieApiRequestTransfer
    {
        $shippingOptionAmountTransfer = new MollieAmountTransfer();
        $shippingOptionAmountTransfer
            ->setCurrency(static::CURRENCY_CODE)
            ->setValue('4.90');

        $shippingOptionTransfer = new MollieExpressCheckoutShippingOptionTransfer();
        $shippingOptionTransfer
            ->setReference('standard')
            ->setDescription('Standard')
            ->setAmount($shippingOptionAmountTransfer);

        $mollieApiRequestTransfer = new MollieApiRequestTransfer();
        $mollieApiRequestTransfer
            ->setQuote($quoteTransfer)
            ->setDescription('Express Checkout Session')
            ->setRedirectUrl('https://shop.example.com/mollie/express-checkout/redirect')
            ->setExpressCheckoutUuid('e1b7a28e-1774-4d00-96ab-c6869d33debb')
            ->setShippingOptions(new ArrayObject([$shippingOptionTransfer]));

        return $mollieApiRequestTransfer;
    }

    /**
     * @param \Mollie\Api\Fake\MockMollieClient $mockMollieClient
     *
     * @return \Mollie\Client\Mollie\MollieClientInterface
     */
    protected function createClient(MockMollieClient $mockMollieClient): MollieClientInterface
    {
        $mollieFactoryMock = $this->createMollieFactoryMock();
        $mollieFactoryMock->method('createMollieApiClient')
            ->willReturn($mockMollieClient);

        return $this->createClientMock($mollieFactoryMock);
    }
}
