<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Mapper\PaymentLink;

use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Mollie\Service\Mollie\MollieServiceInterface;
use Mollie\Zed\Mollie\MollieConfig;

class MolliePaymentLinkMapper implements MolliePaymentLinkMapperInterface
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
    protected const PAYMENT_LINK_FORM_MINIMUM_AMOUNT = 'minimumAmount';

    /**
     * @var string
     */
    protected const PAYMENT_LINK_FORM_PAYMENT_METHODS = 'paymentMethods';

    /**
     * @var string
     */
    protected const PAYMENT_LINK_FORM_EXPIRY_DATE = 'expiryDate';

    /**
     * @param \Mollie\Service\Mollie\MollieServiceInterface $mollieService
     * @param \Mollie\Zed\Mollie\MollieConfig $config
     */
    public function __construct(
        protected MollieServiceInterface $mollieService,
        protected MollieConfig $config,
    ) {
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

        $currency = $formData[static::PAYMENT_LINK_FORM_CURRENCY];
        $mollieAmount = $this->createMollieAmountTransfer($formData[static::PAYMENT_LINK_FORM_AMOUNT] ?? null, $currency);
        $mollieMinimumAmount = $this->createMollieAmountTransfer($formData[static::PAYMENT_LINK_FORM_MINIMUM_AMOUNT] ?? null, $currency);

        $expiryDate = $formData[static::PAYMENT_LINK_FORM_EXPIRY_DATE];
        $expiryDateTime = $expiryDate
            ? $expiryDate->format('Y-m-d\TH:i:sP')
            : $this->mollieService->getPaymentLinkDefaultExpirationDateTime();

        $paymentLinkTransfer
            ->setExpiresAt($expiryDateTime)
            ->setAmount($mollieAmount)
            ->setMinimumAmount($mollieMinimumAmount)
            ->setAllowedMethods($formData[static::PAYMENT_LINK_FORM_PAYMENT_METHODS] ?? []);

        return $paymentLinkTransfer;
    }

    /**
     * @param float|null $amount
     * @param string $currency
     *
     * @return \Generated\Shared\Transfer\MollieAmountTransfer|null
     */
    protected function createMollieAmountTransfer(?float $amount, string $currency): ?MollieAmountTransfer
    {
        if ($amount === null) {
            return null;
        }

        $value = number_format($amount, 2, '.', '');
        $mollieAmountTransfer = new MollieAmountTransfer();
        $mollieAmountTransfer
            ->setValue($value)
            ->setCurrency($currency);

        return $mollieAmountTransfer;
    }
}
