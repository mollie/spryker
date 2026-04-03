<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Mapper;

use ArrayObject;
use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Mollie\Service\Mollie\MollieServiceInterface;
use Mollie\Shared\Mollie\MollieConstants;
use Mollie\Zed\Mollie\MollieConfig;

class MollieCommunicationMapper implements MollieCommunicationMapperInterface
{
    /**
     * @param \Mollie\Service\Mollie\MollieServiceInterface $mollieService
     * @param \Mollie\Zed\Mollie\MollieConfig $config
     */
    public function __construct(
        private MollieServiceInterface $mollieService,
        private MollieConfig $config,
    ) {
    }

    /**
     * @param string $locale
     * @param string|null $currencyCode
     *
     * @return \Generated\Shared\Transfer\MollieApiRequestTransfer
     */
    public function createMollieApiRequestTransfer(string $locale, ?string $currencyCode): MollieApiRequestTransfer
    {
        $amountTransfer = null;

        if ($currencyCode) {
            $amountTransfer = (new MollieAmountTransfer())
                ->setCurrency($currencyCode)
                ->setValue($this->config->getMethodsApiDefaultAmountValue());
        }

        return (new MollieApiRequestTransfer())
            ->setMolliePaymentMethodQueryParameters(
                $this->createMolliePaymentMethodQueryParametersTransfer($locale)
                    ->setAmount($amountTransfer),
            );
    }

        /**
         * @param string $locale
         *
         * @return \Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer
         */
    public function createMolliePaymentMethodQueryParametersTransfer(
        string $locale,
    ): MolliePaymentMethodQueryParametersTransfer {
        return (new MolliePaymentMethodQueryParametersTransfer())
            ->setLocale($locale)
            ->setIncludeIssuers(true)
            ->setSequenceType(MollieConstants::MOLLIE_SEQUENCE_TYPE_ONE_OFF);
    }

    /**
     * @param string|null $molliePaymentKey
     * @param string|null $currencyCode
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer
     */
    public function createMolliePaymentMethodConfigCriteriaTransfer(?string $molliePaymentKey, ?string $currencyCode): MolliePaymentMethodConfigCriteriaTransfer
    {
        return (new MolliePaymentMethodConfigCriteriaTransfer())
            ->setCurrencyCode($currencyCode)
            ->setMollieId($molliePaymentKey);
    }

    /**
     * @param array<\Mollie\Zed\Mollie\Communication\Mapper\MolliePaymentMethodTransfer> $paymentMethodTransfers
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodCollectionTransfer
     */
    public function createMolliePaymentMethodCollection(array $paymentMethodTransfers): MolliePaymentMethodCollectionTransfer
    {
        return (new MolliePaymentMethodCollectionTransfer())->setMethods(
            new ArrayObject($paymentMethodTransfers),
        );
    }

    /**
     * @param float|null $amount
     *
     * @return \Generated\Shared\Transfer\MollieAmountTransfer
     */
    protected function formatMollieAmount(?float $amount): MollieAmountTransfer
    {
        return $amount === null ? new MollieAmountTransfer() : $this->mollieService->convertIntegerToMollieAmount((int)($amount * 100));
    }
}
