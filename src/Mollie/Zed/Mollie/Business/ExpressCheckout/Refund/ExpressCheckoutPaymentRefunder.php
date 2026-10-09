<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\Refund;

use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer;
use Generated\Shared\Transfer\MollieRefundApiResponseTransfer;
use Generated\Shared\Transfer\MollieRefundTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Shared\Mollie\MollieConfig as SharedMollieConfig;

class ExpressCheckoutPaymentRefunder implements ExpressCheckoutPaymentRefunderInterface
{
    /**
     * @var string
     */
    protected const REFUND_DESCRIPTION = 'Express checkout order could not be created';

    /**
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     */
    public function __construct(protected MollieClientInterface $mollieClient)
    {
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer $mollieExpressCheckoutFailedOrderTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundApiResponseTransfer
     */
    public function refund(
        MollieExpressCheckoutFailedOrderTransfer $mollieExpressCheckoutFailedOrderTransfer,
    ): MollieRefundApiResponseTransfer {
        $expressCheckoutUuid = $mollieExpressCheckoutFailedOrderTransfer->getExpressCheckoutUuidOrFail();

        // CreateRefundApi expects the amount value in minor units and converts it to the Mollie decimal format.
        $mollieAmountTransfer = (new MollieAmountTransfer())
            ->setCurrency($mollieExpressCheckoutFailedOrderTransfer->getCurrencyOrFail())
            ->setValue((string)$mollieExpressCheckoutFailedOrderTransfer->getAmountOrFail());

        $mollieRefundTransfer = (new MollieRefundTransfer())
            ->setTransactionId($mollieExpressCheckoutFailedOrderTransfer->getTransactionIdOrFail())
            ->setDescription(static::REFUND_DESCRIPTION)
            ->setMetadata([SharedMollieConfig::EXPRESS_CHECKOUT_METADATA_KEY_UUID => $expressCheckoutUuid])
            ->setAmount($mollieAmountTransfer);

        return $this->mollieClient->createRefund(
            (new MollieApiRequestTransfer())
                ->setIdempotencyKey($expressCheckoutUuid)
                ->setRefund($mollieRefundTransfer),
        );
    }
}
