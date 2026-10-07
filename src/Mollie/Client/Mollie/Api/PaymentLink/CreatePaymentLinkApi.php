<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Api\PaymentLink;

use ArrayObject;
use Generated\Shared\Transfer\MollieAddressTransfer;
use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\MollieLinesTransfer;
use Generated\Shared\Transfer\MollieLinksTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Mollie\Api\Http\Data\Address;
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Data\OrderLine;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\CreatePaymentLinkRequest;
use Mollie\Api\MollieApiClient;
use Mollie\Client\Mollie\Api\AbstractApiCall;
use Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Mollie\Client\Mollie\Logger\MollieLoggerInterface;
use Mollie\Client\Mollie\MollieConfig;
use Mollie\Service\Mollie\MollieServiceInterface;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

class CreatePaymentLinkApi extends AbstractApiCall
{
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
        $paymentLinkTransfer = $mollieApiRequestTransfer->getPaymentLink();

        $description = $paymentLinkTransfer->getDescription();
        $redirectUrl = $paymentLinkTransfer->getRedirectUrl();
        $currency = $paymentLinkTransfer->getCurrency();
        $amount = $this->convertIntegerAmountToMoney($paymentLinkTransfer->getAmount(), $currency);
        $minimumAmount = $this->convertIntegerAmountToMoney($paymentLinkTransfer->getMinimumAmount(), $currency);
        $reusable = $paymentLinkTransfer->getReusable();
        $allowedMethods = $paymentLinkTransfer->getAllowedMethods();
        $expiresAt = $paymentLinkTransfer->getExpiresAt();
        $lines = $this->convertMollieLinesTransfersToDataCollection($paymentLinkTransfer->getLines());
        $billingAddress = $this->convertMollieAddressTransferToAddress($paymentLinkTransfer->getBillingAddress());

        $this->request = new CreatePaymentLinkRequest(
            description: $description,
            amount: $amount,
            minimumAmount: $minimumAmount,
            redirectUrl: $redirectUrl,
            reusable: $reusable,
            expiresAt: $expiresAt,
            allowedMethods: $allowedMethods,
            lines: $lines,
            billingAddress: $billingAddress,
        );

        return $this->request;
    }

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\MollieLinesTransfer> $mollieLinesTransfers
     *
     * @return \Mollie\Api\Http\Data\DataCollection<\Mollie\Api\Http\Data\OrderLine>|null
     */
    protected function convertMollieLinesTransfersToDataCollection(ArrayObject $mollieLinesTransfers): ?DataCollection
    {
        if ($mollieLinesTransfers->count() === 0) {
            return null;
        }

        $orderLines = [];
        foreach ($mollieLinesTransfers as $mollieLinesTransfer) {
            $orderLines[] = $this->convertMollieLinesTransferToOrderLine($mollieLinesTransfer);
        }

        $linesCollection = new DataCollection($orderLines);

        return $linesCollection;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieLinesTransfer $mollieLinesTransfer
     *
     * @return \Mollie\Api\Http\Data\OrderLine
     */
    protected function convertMollieLinesTransferToOrderLine(MollieLinesTransfer $mollieLinesTransfer): OrderLine
    {
        $unitPrice = $this->convertMollieAmountTransferToMoney($mollieLinesTransfer->getUnitPrice());
        $totalAmount = $this->convertMollieAmountTransferToMoney($mollieLinesTransfer->getTotalAmount());
        $vatAmount = $this->convertMollieAmountTransferToMoney($mollieLinesTransfer->getVatAmount());

        $orderLine = new OrderLine(
            description: $mollieLinesTransfer->getDescription(),
            quantity: $mollieLinesTransfer->getQuantity(),
            unitPrice: $unitPrice,
            totalAmount: $totalAmount,
            type: $mollieLinesTransfer->getType(),
            vatRate: $mollieLinesTransfer->getVatRate(),
            vatAmount: $vatAmount,
            sku: $mollieLinesTransfer->getSku(),
        );

        return $orderLine;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieAmountTransfer|null $mollieAmountTransfer
     *
     * @return \Mollie\Api\Http\Data\Money|null
     */
    protected function convertMollieAmountTransferToMoney(?MollieAmountTransfer $mollieAmountTransfer): ?Money
    {
        if ($mollieAmountTransfer === null) {
            return null;
        }

        $money = new Money($mollieAmountTransfer->getCurrency(), $mollieAmountTransfer->getValue());

        return $money;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieAddressTransfer|null $mollieAddressTransfer
     *
     * @return \Mollie\Api\Http\Data\Address|null
     */
    protected function convertMollieAddressTransferToAddress(?MollieAddressTransfer $mollieAddressTransfer): ?Address
    {
        if ($mollieAddressTransfer === null) {
            return null;
        }

        $address = new Address(
            title: $mollieAddressTransfer->getTitle(),
            givenName: $mollieAddressTransfer->getGivenName(),
            familyName: $mollieAddressTransfer->getFamilyName(),
            organizationName: $mollieAddressTransfer->getOrganizationName(),
            streetAndNumber: $mollieAddressTransfer->getStreetAndNumber(),
            streetAdditional: $mollieAddressTransfer->getStreetAdditional(),
            postalCode: $mollieAddressTransfer->getPostalCode(),
            email: $mollieAddressTransfer->getEmail(),
            phone: $mollieAddressTransfer->getPhone(),
            city: $mollieAddressTransfer->getCity(),
            country: $mollieAddressTransfer->getCountry(),
        );

        return $address;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    protected function mapApiResponse(MollieApiResponseTransfer $mollieApiResponseTransfer): AbstractTransfer
    {
        $molliePaymentLinksApiResponseTransfer = (new MolliePaymentLinkApiResponseTransfer())
            ->setIsSuccessful($mollieApiResponseTransfer->getIsSuccessful())
            ->setMessage($mollieApiResponseTransfer->getMessage());

        if (!$mollieApiResponseTransfer->getIsSuccessful()) {
            return $molliePaymentLinksApiResponseTransfer;
        }

        $paymentLinkPayload = $mollieApiResponseTransfer->getPayload();
        $paymentLinkPayloadWithoutAmounts = $this->removeAmountsFromPaymentLinkPayload($paymentLinkPayload);

        $molliePaymentLinkTransfer = new MolliePaymentLinkTransfer();
        $molliePaymentLinkTransfer->fromArray($paymentLinkPayloadWithoutAmounts, true);

        $links = $mollieApiResponseTransfer->getPayload()[MollieConfig::RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS] ?? [];
        $mollieLinksTransfer = new MollieLinksTransfer();
        $mollieLinksTransfer->fromArray($links, true);

        $status = MollieConfig::RESPONSE_CREATE_PAYMENT_LINK_STATUS_OPEN;

        $molliePaymentLinkTransfer
            ->setStatus($status)
            ->setLinks($mollieLinksTransfer);

        $molliePaymentLinksApiResponseTransfer->setMolliePaymentLink($molliePaymentLinkTransfer);

        return $molliePaymentLinksApiResponseTransfer;
    }

    /**
     * @param int|null $amount
     * @param string $currency
     *
     * @return \Mollie\Api\Http\Data\Money|null
     */
    protected function convertIntegerAmountToMoney(?int $amount, string $currency): ?Money
    {
        if ($amount === null) {
            return null;
        }

        $mollieAmountTransfer = $this->mollieService->convertIntegerToMollieAmount($amount, $currency);
        $money = new Money($mollieAmountTransfer->getCurrency(), $mollieAmountTransfer->getValue());

        return $money;
    }

    /**
     * @param array<string, mixed> $paymentLinkPayload
     *
     * @return array<string, mixed>
     */
    protected function removeAmountsFromPaymentLinkPayload(array $paymentLinkPayload): array
    {
        unset($paymentLinkPayload[MollieConfig::RESPONSE_PARAMETER_PAYMENT_LINK_AMOUNT]);
        unset($paymentLinkPayload[MollieConfig::RESPONSE_PARAMETER_PAYMENT_LINK_MINIMUM_AMOUNT]);

        return $paymentLinkPayload;
    }
}
