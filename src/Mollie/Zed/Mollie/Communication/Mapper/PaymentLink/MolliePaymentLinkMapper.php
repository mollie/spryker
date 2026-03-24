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
    protected const PAYMENT_LINK_FORM_PAYMENT_METHODS = 'paymentMethods';

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

        $value = number_format($formData[static::PAYMENT_LINK_FORM_AMOUNT], 2);
        $mollieAmount = new MollieAmountTransfer();
        $mollieAmount
            ->setValue($value)
            ->setCurrency($formData[static::PAYMENT_LINK_FORM_CURRENCY]);

        $expiryDateTime = $this->mollieService->getPaymentLinkDefaultExpirationDateTime();

        $paymentLinkTransfer
            ->setExpiresAt($expiryDateTime)
            ->setAmount($mollieAmount)
            ->setAllowedMethods($formData[static::PAYMENT_LINK_FORM_PAYMENT_METHODS] ?? []);

        return $paymentLinkTransfer;
    }
}
