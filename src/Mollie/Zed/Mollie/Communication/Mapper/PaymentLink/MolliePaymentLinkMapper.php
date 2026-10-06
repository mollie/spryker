<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Mapper\PaymentLink;

use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Mollie\Service\Mollie\MollieServiceInterface;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToMoneyFacadeInterface;
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
     * @param \Mollie\Zed\Mollie\Dependency\Facade\MollieToMoneyFacadeInterface $moneyFacade
     */
    public function __construct(
        protected MollieServiceInterface $mollieService,
        protected MollieConfig $config,
        protected MollieToMoneyFacadeInterface $moneyFacade,
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
        $amount = $this->convertDecimalAmountToInteger($formData[static::PAYMENT_LINK_FORM_AMOUNT] ?? null);
        $minimumAmount = $this->convertDecimalAmountToInteger($formData[static::PAYMENT_LINK_FORM_MINIMUM_AMOUNT] ?? null);

        $expiryDate = $formData[static::PAYMENT_LINK_FORM_EXPIRY_DATE];
        $expiryDateTime = $expiryDate
            ? $expiryDate->format('Y-m-d\TH:i:sP')
            : $this->mollieService->getPaymentLinkDefaultExpirationDateTime();

        $paymentLinkTransfer
            ->setExpiresAt($expiryDateTime)
            ->setAmount($amount)
            ->setMinimumAmount($minimumAmount)
            ->setCurrency($currency)
            ->setAllowedMethods($formData[static::PAYMENT_LINK_FORM_PAYMENT_METHODS] ?? []);

        return $paymentLinkTransfer;
    }

    /**
     * @param float|null $decimalAmount
     *
     * @return int|null
     */
    protected function convertDecimalAmountToInteger(?float $decimalAmount): ?int
    {
        if ($decimalAmount === null) {
            return null;
        }

        $amount = $this->moneyFacade->convertDecimalToInteger($decimalAmount);

        return $amount;
    }
}
