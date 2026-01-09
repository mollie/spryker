<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Api\Payment;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\MollieCreatePaymentApiResponseTransfer;
use Generated\Shared\Transfer\MollieLinksTransfer;
use Generated\Shared\Transfer\MolliePaymentApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Api\MollieApiClient;
use Mollie\Client\Mollie\Api\AbstractApiCall;
use Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Mollie\Client\Mollie\MollieConfig;
use Mollie\Service\Mollie\MollieServiceInterface;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Shared\Log\LoggerTrait;

class CreatePaymentApi extends AbstractApiCall
{
    use LoggerTrait;

    /**
     * @param \Mollie\Api\MollieApiClient $mollieApiClient
     * @param \Mollie\Client\Mollie\MollieConfig $mollieConfig
     * @param \Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface $utilEncodingService
     * @param \Mollie\Service\Mollie\MollieServiceInterface $mollieService
     */
    public function __construct(
        MollieApiClient $mollieApiClient,
        MollieConfig $mollieConfig,
        MollieToUtilEncodingServiceInterface $utilEncodingService,
        protected MollieServiceInterface $mollieService,
    ) {
        parent::__construct($mollieApiClient, $mollieConfig, $utilEncodingService);
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
        $method = $this->mollieConfig->getMolliePaymentMethod($paymentTransfer->getPaymentMethod());
        $metadata = $this->addMetadata($checkoutResponseTransfer);
        $additionalParameters = $this->addAdditionalParameters($mollieApiRequestTransfer);

        return new CreatePaymentRequest(
            description: $checkoutResponseTransfer->getSaveOrderOrFail()->getOrderReference(),
            amount: $amount,
            redirectUrl: $redirectUrl,
            //webhookUrl: $this->mollieConfig->getMollieWebhookUrl(),
            method: $method,
            metadata: $metadata,
            additional: $additionalParameters,
        );
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
        if ($paymentTransfer->getMollieCreditCardPayment()) {
            $additionalData[MollieConfig::REQUEST_PARAMETER_CREATE_PAYMENT_CARD_TOKEN] = $paymentTransfer->getMollieCreditCardPayment()->getCardToken();
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
        return (string)$this->mollieService->convertIntegerToDecimal($amount);
        //return bcdiv((string)$amount, '100', 2);
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

        $mollieCreatePaymentApiResponseTransfer = new MollieCreatePaymentApiResponseTransfer();
        $mollieCreatePaymentApiResponseTransfer->fromArray($mollieApiResponseTransfer->getPayload(), true);

        $links = $mollieApiResponseTransfer->getPayload()[MollieConfig::RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS] ?? null;
        $mollieLinksTransfer = new MollieLinksTransfer();
        $mollieLinksTransfer->fromArray($links, true);

        $mollieCreatePaymentApiResponseTransfer
            ->setLinks($mollieLinksTransfer)
            ->setEmbedded($mollieApiResponseTransfer->getPayload()[MollieConfig::RESPONSE_PARAMETER_CREATE_PAYMENT_EMBEDDED] ?? null);

        $molliePaymentTransfer = new MolliePaymentTransfer();
        $molliePaymentTransfer->fromArray($mollieCreatePaymentApiResponseTransfer->toArray(), true);

        $molliePaymentApiResponseTransfer->setMolliePayment($molliePaymentTransfer);

        return $molliePaymentApiResponseTransfer;
    }
}
