<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Mapper;

use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Mollie\Service\Mollie\MollieServiceInterface;
use Mollie\Shared\Mollie\MollieConstants;

class MollieCommunicationMapper implements MollieCommunicationMapperInterface
{
    /**
     * @param \Mollie\Service\Mollie\MollieServiceInterface $mollieService
     */
    public function __construct(
        private MollieServiceInterface $mollieService,
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
                ->setValue('100.00');
        }

        return (new MollieApiRequestTransfer())
            ->setMolliePaymentMethodQueryParameters(
                $this->createMolliePaymentMethodQueryParametersTransfer($locale, $amountTransfer),
            );
    }

        /**
         * @param string $locale
         * @param \Generated\Shared\Transfer\MollieAmountTransfer|null $amountTransfer
         *
         * @return \Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer
         */
    public function createMolliePaymentMethodQueryParametersTransfer(
        string $locale,
        ?MollieAmountTransfer $amountTransfer,
    ): MolliePaymentMethodQueryParametersTransfer {
        return (new MolliePaymentMethodQueryParametersTransfer())
            ->setLocale($locale)
            ->setIncludeIssuers(true)
            ->setAmount($amountTransfer)
            ->setSequenceType(MollieConstants::MOLLIE_SEQUENCE_TYPE_ONE_OFF);
    }

    /**
     * @param array<string, mixed> $formData
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer|null $configTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer
     */
    public function mapFormDataToPaymentMethodConfigTransfer(
        array $formData,
        ?MolliePaymentMethodConfigTransfer $configTransfer,
    ): MolliePaymentMethodConfigTransfer {
        if (!$configTransfer) {
            $configTransfer = new MolliePaymentMethodConfigTransfer();
        }

        $configTransfer
            ->setIsActive($formData[MolliePaymentMethodConfigTransfer::IS_ACTIVE])
            ->setStatus($formData[MolliePaymentMethodConfigTransfer::IS_ACTIVE] ? 'activated' : 'not activated')
            ->setIsLogoVisible($formData[MolliePaymentMethodConfigTransfer::IS_LOGO_VISIBLE])
            ->setMollieId($formData[MolliePaymentMethodConfigTransfer::MOLLIE_ID]);

        if ($formData[MolliePaymentMethodConfigTransfer::IMAGE]) {
            $configTransfer->setImage(['size2x' => $formData[MolliePaymentMethodConfigTransfer::IMAGE]]);
        }

        $configTransfer->setMaximumAmount($this->formatMollieAmount($formData[MolliePaymentMethodConfigTransfer::MAXIMUM_AMOUNT]));
        $configTransfer->setMinimumAmount($this->formatMollieAmount($formData[MolliePaymentMethodConfigTransfer::MINIMUM_AMOUNT]));

        return $configTransfer;
    }

    /**
     * @param string $molliePaymentKey
     * @param string $currencyCode
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer
     */
    public function createMolliePaymentMethodConfigCriteriaTransfer(string $molliePaymentKey, string $currencyCode): MolliePaymentMethodConfigCriteriaTransfer
    {
        return (new MolliePaymentMethodConfigCriteriaTransfer())
            ->setCurrencyCode($currencyCode)
            ->setMollieId($molliePaymentKey);
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
