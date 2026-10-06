<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Processor\PaymentLink;

use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Mollie\Service\Mollie\MollieServiceInterface;
use Mollie\Zed\Mollie\Business\Mapper\PaymentLink\PaymentLinkOrderMapperInterface;
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
     * @param \Mollie\Zed\Mollie\Business\Mapper\PaymentLink\PaymentLinkOrderMapperInterface $paymentLinkOrderMapper
     */
    public function __construct(
        protected MollieServiceInterface $mollieService,
        protected MollieConfig $config,
        protected PaymentLinkOrderMapperInterface $paymentLinkOrderMapper,
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

        $totalsTransfer = $orderTransfer->getTotals();
        $grandTotal = $totalsTransfer->getGrandTotal();
        $currencyTransfer = $orderTransfer->getCurrency();
        $currencyCode = $currencyTransfer->getCode();
        $expirationDateTime = $this->mollieService->getPaymentLinkDefaultExpirationDateTime();
        $mollieLinesTransfers = $this->paymentLinkOrderMapper->mapOrderItemsAndExpensesToMollieLines($orderTransfer);
        $mollieBillingAddressTransfer = $this->paymentLinkOrderMapper->mapOrderBillingAddressToMollieAddress($orderTransfer);

        $molliePaymentLinkTransfer
            ->setFkSalesOrder($orderTransfer->getIdSalesOrder())
            ->setDescription(sprintf(static::MOLLIE_PAYMENT_LINK_DESCRIPTION, $orderTransfer->getOrderReference()))
            ->setAmount($grandTotal)
            ->setCurrency($currencyCode)
            ->setExpiresAt($expirationDateTime)
            ->setLines($mollieLinesTransfers)
            ->setBillingAddress($mollieBillingAddressTransfer);

        return $molliePaymentLinkTransfer;
    }
}
