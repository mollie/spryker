<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Mapper;

use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Mollie\Shared\Mollie\MollieConstants;

class MollieCommunicationMapper implements MollieCommunicationMapperInterface
{
    /**
     * @var string
     */
    protected const PAYMENT_LINK_FORM_CURRENCY = 'currency';

    /**
     * @var string
     */
    protected const PAYMENT_LINK_FORM_AMOUNT = 'amount';

    /**
     * @var string
     */
    protected const PAYMENT_LINK_FORM_PAYMENT_METHODS = 'paymentMethods';

    /**
     * @param string $locale
     *
     * @return \Generated\Shared\Transfer\MollieApiRequestTransfer
     */
    public function createMollieApiRequestTransfer(string $locale): MollieApiRequestTransfer
    {
        return (new MollieApiRequestTransfer())
            ->setMolliePaymentMethodQueryParameters(
                $this->createMolliePaymentMethodQueryParametersTransfer($locale),
            );
    }

        /**
         * @param string $locale
         *
         * @return \Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer
         */
    public function createMolliePaymentMethodQueryParametersTransfer(string $locale): MolliePaymentMethodQueryParametersTransfer
    {
        return (new MolliePaymentMethodQueryParametersTransfer())
            ->setLocale($locale)
            ->setIncludeIssuers(true)
            ->setSequenceType(MollieConstants::MOLLIE_SEQUENCE_TYPE_ONE_OFF);
    }

    /**
     * @param array<string, mixed> $formData
     *
     * @return \Generated\Shared\Transfer\MolliePaymentLinkTransfer
     */
    public function mapPaymentLinkFormDataToMolliePaymentLinkTransfer(array $formData): MolliePaymentLinkTransfer
    {
        $paymentLinkTransfer = new MolliePaymentLinkTransfer();
        $paymentLinkTransfer->fromArray($formData, true);

        $value = number_format($formData[static::PAYMENT_LINK_FORM_AMOUNT], 2);
        $mollieAmount = new MollieAmountTransfer();
        $mollieAmount
            ->setValue($value)
            ->setCurrency($formData[static::PAYMENT_LINK_FORM_CURRENCY]);

        $paymentLinkTransfer
            ->setAmount($mollieAmount)
            ->setAllowedMethods($formData[static::PAYMENT_LINK_FORM_PAYMENT_METHODS] ?? []);

        return $paymentLinkTransfer;
    }
}
