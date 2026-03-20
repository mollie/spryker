<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Processor\PaymentLink;

use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Mollie\Service\Mollie\MollieServiceInterface;
use Mollie\Zed\Mollie\MollieConfig;

class PaymentLinkProcessor implements PaymentLinkProcessorInterface
{
    /**
     * @var string
     */
    protected const MOLLIE_PAYMENT_LINK_DESCRIPTION = 'Payment link - Order %s';

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
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentLinkTransfer
     */
    public function processOrderItemPaymentLink(OrderTransfer $orderTransfer): MolliePaymentLinkTransfer
    {
        $molliePaymentLinkTransfer = new MolliePaymentLinkTransfer();

        $amountTransfer = $this->getMollieAmount($orderTransfer);
        $expirationDateTime = $this->mollieService->getPaymentLinkDefaultExpirationDateTime();

        $molliePaymentLinkTransfer
            ->setFkSalesOrder($orderTransfer->getIdSalesOrder())
            ->setDescription(sprintf(static::MOLLIE_PAYMENT_LINK_DESCRIPTION, $orderTransfer->getOrderReference()))
            ->setAmount($amountTransfer)
            ->setWebhookUrl($this->config->getMollieWebhookUrl()) // change this to next gen webhook
            ->setExpiresAt($expirationDateTime);

        return $molliePaymentLinkTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\MollieAmountTransfer
     */
    protected function getMollieAmount(OrderTransfer $orderTransfer): MollieAmountTransfer
    {
        $totalsTransfer = $orderTransfer->getTotals();
        $grandTotal = $totalsTransfer->getGrandTotal();
        $currency = $orderTransfer->getCurrency()->getCode();

        $amountTransfer = $this->mollieService->convertIntegerToMollieAmount($grandTotal);
        $amountTransfer->setCurrency($currency);

        return $amountTransfer;
    }
}
