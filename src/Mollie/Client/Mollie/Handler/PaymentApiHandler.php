<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Handler;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieLinesTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Api\Http\Data\Address;
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Client\Mollie\MollieConfig;
use Mollie\Service\Mollie\MollieServiceInterface;
use Mollie\Shared\Mollie\MollieConfig as SharedConfig;
use Mollie\Shared\Mollie\MollieConstants;
use Spryker\Shared\Shipment\ShipmentConfig;

class PaymentApiHandler implements PaymentApiHandlerInterface
{
    /**
     * @param \Mollie\Service\Mollie\MollieServiceInterface $mollieService
     * @param \Mollie\Client\Mollie\MollieConfig $mollieConfig
     */
    public function __construct(
        protected MollieServiceInterface $mollieService,
        protected MollieConfig $mollieConfig,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return array<string>
     */
    public function createPaymentMetadata(CheckoutResponseTransfer $checkoutResponseTransfer): array
    {
        return [MollieConfig::REQUEST_PARAMETER_CREATE_PAYMENT_ORDER_REFERENCE => $checkoutResponseTransfer->getSaveOrderOrFail()->getOrderReference()];
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return array<string, string>
     */
    public function createAdditionalParameters(MollieApiRequestTransfer $mollieApiRequestTransfer): array
    {
        $additionalData = [];
        $paymentTransfer = $mollieApiRequestTransfer->getQuote()->getPayment();
        switch ($paymentTransfer->getPaymentMethod()) {
            case SharedConfig::MOLLIE_PAYMENT_CREDIT_CARD:
                $additionalData[MollieConfig::REQUEST_PARAMETER_CREATE_PAYMENT_CARD_TOKEN] = $paymentTransfer->getMollieCreditCardPayment()->getCardToken();

                break;
            case SharedConfig::MOLLIE_PAYMENT_PAYPAL:
                $additionalData[MollieConfig::REQUEST_PARAMETER_CREATE_PAYMENT_PAYPAL_SESSION_ID] = $paymentTransfer->getMolliePayPalPayment()->getSessionId() ?? '';
                $additionalData[MollieConfig::REQUEST_PARAMETER_CREATE_PAYMENT_PAYPAL_DIGITAL_GOODS] = $paymentTransfer->getMolliePayPalPayment()->getDigitalGoods() ?? false;

                break;
            case SharedConfig::MOLLIE_PAYMENT_BANK_TRANSFER:
                $additionalData[MollieConfig::REQUEST_PARAMETER_CREATE_PAYMENT_BANK_TRANSFER_DUE_DATE] = $paymentTransfer->getMollieBankTransferPayment()->getDueDate() ?? '';
                $additionalData[MollieConfig::REQUEST_PARAMETER_CREATE_PAYMENT_BANK_TRANSFER_BILLING_EMAIL] = $paymentTransfer->getMollieBankTransferPayment()->getBillingEmail() ?? '';

                break;
            case SharedConfig::MOLLIE_PAYMENT_KLARNA:
                $additionalData[MollieConfig::REQUEST_PARAMETER_CREATE_PAYMENT_KLARNA_EXTRA_MERCHANT_DATA] = $paymentTransfer->getMollieKlarnaPayment()->getExtraMerchantData() ?? '';

                break;
            case SharedConfig::MOLLIE_PAYMENT_APPLE_PAY:
                $additionalData[MollieConfig::REQUEST_PARAMETER_CREATE_PAYMENT_APPLE_PAY_PAYMENT_TOKEN] = $paymentTransfer->getMollieApplePayPayment()->getApplePayPaymentToken() ?? '';

                break;
            case SharedConfig::MOLLIE_PAYMENT_IDEAL_IN3:
                $additionalData[MollieConfig::REQUEST_PARAMETER_CREATE_PAYMENT_IDEAL_IN3_CONSUMER_DATE_OF_BIRTH] = $mollieApiRequestTransfer->getQuote()->getCustomer()?->getDateOfBirth();

                break;
            default:
                break;
        }

        return $additionalData;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Mollie\Api\Http\Data\Address
     */
    public function createBillingAddress(QuoteTransfer $quoteTransfer): Address
    {
        $customerAddress = $quoteTransfer->getBillingAddress();

        $billingAddress = new Address(
            title: $customerAddress->getSalutation(),
            givenName: $customerAddress->getFirstName(),
            familyName: $customerAddress->getLastName(),
            organizationName: $customerAddress->getCompany(),
            streetAndNumber: $customerAddress->getAddress1(),
            postalCode: $customerAddress->getZipCode(),
            email: $customerAddress->getEmail() ?? $quoteTransfer->getCustomer()?->getEmail(),
            phone: $customerAddress->getPhone(),
            city: $customerAddress->getCity(),
            country: $customerAddress->getIso2Code(),
        );

        return $billingAddress;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param string $method
     *
     * @return \Mollie\Api\Http\Data\DataCollection<array<mixed>>|null
     */
    public function createLines(QuoteTransfer $quoteTransfer, string $method): ?DataCollection
    {
        if (!in_array($method, $this->mollieConfig->getBNPLPaymentMethods())) {
            return null;
        }

        $items = $quoteTransfer->getItems();
        $currencyCode = $quoteTransfer->getCurrency()->getCode();

        $lines = [];
        foreach ($items as $item) {
            $linesTransfer = new MollieLinesTransfer();

            $unitPrice = $this->mollieService->convertIntegerToMollieAmount($item->getUnitPrice(), $currencyCode);
            $totalAmount = $this->mollieService->convertIntegerToMollieAmount($item->getSumPriceToPayAggregation(), $currencyCode);
            $discountAmount = $this->mollieService->convertIntegerToMollieAmount($item->getUnitDiscountAmountAggregation(), $currencyCode);
            $vatRate = number_format($item->getTaxRate(), 2);
            $vatAmount = $this->mollieService->convertIntegerToMollieAmount($item->getUnitTaxAmount(), $currencyCode);

            $linesTransfer
                ->setType(MollieConstants::PRODUCT_TYPE_PHYSICAL)
                ->setDescription($item->getName())
                ->setQuantity($item->getQuantity())
                ->setUnitPrice($unitPrice)
                ->setTotalAmount($totalAmount)
                ->setDiscountAmount($discountAmount)
                ->setVatRate($vatRate)
                ->setVatAmount($vatAmount)
                ->setSku($item->getSku());

            $lines[] = $linesTransfer->toArray(true, true);
        }

        $shippingFee = $this->getShippingFee($quoteTransfer);
        if ($shippingFee) {
            $lines[] = $shippingFee;
        }

        $linesCollection = new DataCollection($lines);

        return $linesCollection;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array<string, mixed>|null
     */
    protected function getShippingFee(QuoteTransfer $quoteTransfer): ?array
    {
        $shippingExpense = $this->getShippingExpense($quoteTransfer);
        $shippingAmount = $shippingExpense?->getSumPriceToPayAggregation();

        if (!$shippingAmount) {
            return null;
        }

        $currencyCode = $quoteTransfer->getCurrency()->getCode();
        $shippingFee = $this->mollieService->convertIntegerToMollieAmount($shippingAmount, $currencyCode);

        $linesTransfer = new MollieLinesTransfer();
        $linesTransfer
            ->setType(MollieConstants::PRODUCT_TYPE_SHIPPING_FEE)
            ->setDescription('Shipping Fee')
            ->setQuantity(1)
            ->setUnitPrice($shippingFee)
            ->setTotalAmount($shippingFee);

        return $linesTransfer->toArray(true, true);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\ExpenseTransfer|null
     */
    protected function getShippingExpense(QuoteTransfer $quoteTransfer): ?ExpenseTransfer
    {
        foreach ($quoteTransfer->getExpenses() as $expense) {
            if ($expense->getType() === ShipmentConfig::SHIPMENT_EXPENSE_TYPE) {
                return $expense;
            }
        }

        return null;
    }
}
