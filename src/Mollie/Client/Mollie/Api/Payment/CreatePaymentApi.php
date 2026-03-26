<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie\Api\Payment;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\MollieLinesTransfer;
use Generated\Shared\Transfer\MollieLinksTransfer;
use Generated\Shared\Transfer\MolliePaymentApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Api\Http\Data\Address;
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Api\MollieApiClient;
use Mollie\Client\Mollie\Api\AbstractApiCall;
use Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Mollie\Client\Mollie\Logger\MollieLoggerInterface;
use Mollie\Client\Mollie\MollieConfig;
use Mollie\Service\Mollie\MollieServiceInterface;
use Mollie\Shared\Mollie\MollieConfig as SharedConfig;
use Mollie\Shared\Mollie\MollieConstants;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Shared\Log\LoggerTrait;

class CreatePaymentApi extends AbstractApiCall
{
    use LoggerTrait;

    /**
     * @param \Mollie\Api\MollieApiClient $mollieApiClient
     * @param \Mollie\Client\Mollie\MollieConfig $mollieConfig
     * @param \Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface $utilEncodingService
     * @param \Mollie\Client\Mollie\Logger\MollieLoggerInterface $logger
     * @param \Mollie\Service\Mollie\MollieServiceInterface $mollieService
     */
    public function __construct(
        MollieApiClient $mollieApiClient,
        MollieConfig $mollieConfig,
        MollieToUtilEncodingServiceInterface $utilEncodingService,
        MollieLoggerInterface $logger,
        protected MollieServiceInterface $mollieService,
    ) {
        parent::__construct($mollieApiClient, $mollieConfig, $utilEncodingService, $logger);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer|null $mollieApiRequestTransfer
     *
     * @return \Mollie\Api\Http\Request|null
     */
    protected function buildRequest(?MollieApiRequestTransfer $mollieApiRequestTransfer = null): ?Request
    {
        $checkoutResponseTransfer = $mollieApiRequestTransfer->getCheckoutResponse();
        $quoteTransfer = $mollieApiRequestTransfer->getQuote();
        $paymentTransfer = $quoteTransfer->getPayment();

        $value = $this->convertAmountToString($paymentTransfer->getAmount());
        $amount = new Money(
            currency: $quoteTransfer->getCurrency()->getCode(),
            value: $value,
        );
        $redirectUrl = $this->getRedirectUrl($checkoutResponseTransfer->getSaveOrderOrFail()->getOrderReference());

        $webhookUrl = $this->mollieService->resolveWebhookUrl(
            $this->mollieConfig->getMollieWebhookUrl(),
            $this->mollieConfig->getTestEnvironmentMollieWebhookUrl(),
            $this->mollieConfig->isMollieTestModeEnabled(),
        );

        $method = $this->mollieConfig->getMolliePaymentMethod($paymentTransfer->getPaymentMethod());
        $metadata = $this->addMetadata($checkoutResponseTransfer);
        $additionalParameters = $this->addAdditionalParameters($mollieApiRequestTransfer);
        $billingAddress = $this->addBillingAddress($quoteTransfer);

        $lines = null;
        if (in_array($method, $this->mollieConfig->getBNPLPaymentMethods())) {
            $lines = $this->addLines($quoteTransfer);
        }

        $this->request = new CreatePaymentRequest(
            description: $checkoutResponseTransfer->getSaveOrderOrFail()->getOrderReference(),
            amount: $amount,
            redirectUrl: $redirectUrl,
            webhookUrl: $webhookUrl,
            method: $method,
            metadata: $metadata,
            captureMode: $this->getCaptureModeForMethod($method),
            additional: $additionalParameters,
            lines: $lines,
            billingAddress: $billingAddress,
        );

        return $this->request;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return array<string, string>
     */
    protected function addAdditionalParameters(MollieApiRequestTransfer $mollieApiRequestTransfer): array
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
            case SharedConfig::MOLLIE_PAYMENT_BILLIE:
                //$additionalData[MollieConfig::REQUEST_PARAMETER_CREATE_PAYMENT_BILLIE_COMPANY] = $this->createCompanyObject($mollieApiRequestTransfer);

                break;
            case SharedConfig::MOLLIE_PAYMENT_IDEAL_IN3:
                $additionalData[MollieConfig::REQUEST_PARAMETER_CREATE_PAYMENT_IDEAL_IN3_CONSUMER_DATE_OF_BIRTH] = $this->getCustomerDateOfBirth($mollieApiRequestTransfer);

                break;
            default:
                break;
        }

        return $additionalData;
    }

    /**
     * @param int $amount
     *
     * @return string
     */
    protected function convertAmountToString(int $amount): string
    {
        $mollieAmountTransfer = $this->mollieService->convertIntegerToMollieAmount($amount);

        return $mollieAmountTransfer->getValue();
    }

    /**
     * @param string $orderReference
     *
     * @return string
     */
    protected function getRedirectUrl(string $orderReference): string
    {
        return $this->mollieConfig->getMollieRedirectUrl() . '?orderReference=' . $orderReference;
    }

    /**
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return array<string>
     */
    protected function addMetadata(CheckoutResponseTransfer $checkoutResponseTransfer): array
    {
        return [MollieConfig::REQUEST_PARAMETER_CREATE_PAYMENT_ORDER_REFERENCE => $checkoutResponseTransfer->getSaveOrderOrFail()->getOrderReference()];
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    protected function mapApiResponse(MollieApiResponseTransfer $mollieApiResponseTransfer): AbstractTransfer
    {
        $molliePaymentApiResponseTransfer = (new MolliePaymentApiResponseTransfer())
            ->setIsSuccessful($mollieApiResponseTransfer->getIsSuccessful())
            ->setMessage($mollieApiResponseTransfer->getMessage());

        $molliePaymentTransfer = new MolliePaymentTransfer();
        $molliePaymentTransfer->fromArray($mollieApiResponseTransfer->getPayload(), true);

        $links = $mollieApiResponseTransfer->getPayload()[MollieConfig::RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS] ?? [];
        $mollieLinksTransfer = new MollieLinksTransfer();
        $mollieLinksTransfer->fromArray($links, true);
        $molliePaymentTransfer
            ->setLinks($mollieLinksTransfer);

        $molliePaymentApiResponseTransfer->setMolliePayment($molliePaymentTransfer);

        return $molliePaymentApiResponseTransfer;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getRequestBody(): array
    {
         /** @var \Mollie\Api\Http\Requests\CreatePaymentRequest|null $createPaymentRequest */
        $createPaymentRequest = $this->request;

        if (!$createPaymentRequest) {
            return [];
        }

        $payload = $createPaymentRequest->payload();
        $requestBody = $payload->all();

        return $requestBody;
    }

    /**
     * @param string $method
     *
     * @return string
     */
    protected function getCaptureModeForMethod(string $method): string
    {
        $molliePaymentMethodsManualCapture = $this->mollieConfig->getMolliePaymentMethodsManualCapture();
        if (in_array($method, $molliePaymentMethodsManualCapture)) {
            return $this->mollieConfig->getMollieManualCaptureMode();
        }

        return $this->mollieConfig->getMollieAutomaticCaptureMode();
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Mollie\Api\Http\Data\DataCollection<array<mixed>>
     */
    protected function addLines(QuoteTransfer $quoteTransfer): DataCollection
    {
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

        $linesTransfer = new MollieLinesTransfer();
        $shippingFee = $this->mollieService->convertIntegerToMollieAmount($quoteTransfer->getTotals()->getShipmentTotal(), $currencyCode);
        $linesTransfer
            ->setType(MollieConstants::PRODUCT_TYPE_SHIPPING_FEE)
            ->setDescription('Shipping Fee')
            ->setQuantity(1)
            ->setUnitPrice($shippingFee)
            ->setTotalAmount($shippingFee);
        $lines[] = $linesTransfer->toArray(true, true);

        $linesCollection = new DataCollection($lines);

        return $linesCollection;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Mollie\Api\Http\Data\Address
     */
    protected function addBillingAddress(QuoteTransfer $quoteTransfer): Address
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
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return array<string>
     */
    protected function createCompanyObject(MollieApiRequestTransfer $mollieApiRequestTransfer): array
    {
        //$quoteTransfer = $mollieApiRequestTransfer->getQuote();
        //$billingAddress = $quoteTransfer->getBillingAddress();

        //$company[MollieConfig::REQUEST_PARAMETER_CREATE_PAYMENT_BILLIE_COMPANY_BILLING_ADDRESS_ORGANIZATION_NAME] = $billingAddress->getCompany();
        //$company['billingAddress'] = ['organizationName' => $billingAddress->getCompany()];
        //$company[MollieConfig::REQUEST_PARAMETER_CREATE_PAYMENT_BILLIE_COMPANY_REGISTRATION_NUMBER] = 'reg 123'; // no company registration number in spryker?
        //$company[MollieConfig::REQUEST_PARAMETER_CREATE_PAYMENT_BILLIE_COMPANY_VAT_NUMBER] = 'test 123'; // no vat numbers in spryker?
        //$company[MollieConfig::REQUEST_PARAMETER_CREATE_PAYMENT_BILLIE_COMPANY_ENTITY_TYPE] = 'gmbh'; // no company entity type

        return [];
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return string|null
     */
    protected function getCustomerDateOfBirth(MollieApiRequestTransfer $mollieApiRequestTransfer): ?string
    {
        $dateOfBirth = null;
        $quoteTransfer = $mollieApiRequestTransfer->getQuote();
        if ($quoteTransfer->getCustomer()) {
            $dateOfBirth = $quoteTransfer->getCustomer()->getDateOfBirth();
        }

        return $dateOfBirth;
    }
}
