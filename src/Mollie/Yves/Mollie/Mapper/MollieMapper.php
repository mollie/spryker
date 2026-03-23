<?php

declare(strict_types = 1);

namespace Mollie\Yves\Mollie\Mapper;

use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieCacheOptionsTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Generated\Shared\Transfer\MollieWebhookEventTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Service\Mollie\MollieServiceInterface;
use Mollie\Shared\Mollie\MollieConstants;
use Mollie\Yves\Mollie\Dependency\Client\MollieToLocaleClientInterface;
use Mollie\Yves\Mollie\MollieConfig;

class MollieMapper implements MollieMapperInterface
{
    /**
     * @param \Mollie\Service\Mollie\MollieServiceInterface $mollieService
     * @param \Mollie\Yves\Mollie\Dependency\Client\MollieToLocaleClientInterface $localeClient
     * @param \Mollie\Yves\Mollie\MollieConfig $mollieConfig
     */
    public function __construct(
        protected MollieServiceInterface $mollieService,
        protected MollieToLocaleClientInterface $localeClient,
        protected MollieConfig $mollieConfig,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\MollieCacheOptionsTransfer $mollieCacheOptionsTransfer
     *
     * @return \Generated\Shared\Transfer\MollieApiRequestTransfer
     */
    public function createMollieApiRequestTransfer(MollieCacheOptionsTransfer $mollieCacheOptionsTransfer): MollieApiRequestTransfer
    {
        return (new MollieApiRequestTransfer())
            ->setMolliePaymentMethodQueryParameters(
                $this->createMolliePaymentMethodQueryParametersTransfer($mollieCacheOptionsTransfer),
            );
    }

    /**
     * @param \Generated\Shared\Transfer\MollieCacheOptionsTransfer $mollieCacheOptionsTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer
     */
    public function createMolliePaymentMethodQueryParametersTransfer(
        MollieCacheOptionsTransfer $mollieCacheOptionsTransfer,
    ): MolliePaymentMethodQueryParametersTransfer {
        return (new MolliePaymentMethodQueryParametersTransfer())
            ->setLocale($mollieCacheOptionsTransfer->getLocale())
            ->setIncludeIssuers(true)
            ->setBillingCountry($mollieCacheOptionsTransfer->getBillingCountry())
            ->setAmount($mollieCacheOptionsTransfer->getAmount())
            ->setIncludeWallets($this->mollieConfig->getMollieIncludeWallets())
            ->setSequenceType(MollieConstants::MOLLIE_SEQUENCE_TYPE_ONE_OFF);
    }

    /**
     * @param array<string, mixed> $requestBody
     *
     * @return \Generated\Shared\Transfer\MollieWebhookEventTransfer
     */
    public function mapRequestPayloadToMollieWebhookEventTransfer(array $requestBody): MollieWebhookEventTransfer
    {
        $mollieWebhookEventTransfer = (new MollieWebhookEventTransfer())
            ->fromArray($requestBody, true);

        $embedded = $requestBody['_embedded'] ?? [];
        $mollieWebhookEventTransfer->setEmbedded($embedded);

        return $mollieWebhookEventTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\MollieCacheOptionsTransfer
     */
    public function createMollieCacheOptionsTransfer(QuoteTransfer $quoteTransfer): MollieCacheOptionsTransfer
    {
        $mollieCacheOptionsTransfer = new MollieCacheOptionsTransfer();
        $locale = $this->localeClient->getCurrentLocale();
        $billingCountry = $quoteTransfer->getBillingAddress()->getIso2Code();
        $currency = $quoteTransfer->getCurrency()?->getCode();

        $mollieAmount = (new MollieAmountTransfer());
        $grandTotal = $quoteTransfer->getTotals()->getGrandTotal();
        $amountValue = number_format($this->mollieService->convertIntegerToDecimal($grandTotal), 2);
        $mollieAmount
            ->setValue($amountValue)
            ->setCurrency($currency);

        $mollieCacheOptionsTransfer
            ->setLocale($locale)
            ->setBillingCountry($billingCountry)
            ->setAmount($mollieAmount);

        return $mollieCacheOptionsTransfer;
    }
}
