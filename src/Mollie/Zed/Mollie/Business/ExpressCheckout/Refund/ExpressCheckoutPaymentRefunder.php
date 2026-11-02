<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\Refund;

use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer;
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
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer $mollieExpressCheckoutPaymentRefundTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundApiResponseTransfer
     */
    public function refund(
        MollieExpressCheckoutPaymentRefundTransfer $mollieExpressCheckoutPaymentRefundTransfer,
    ): MollieRefundApiResponseTransfer {
        $expressCheckoutUuid = $mollieExpressCheckoutPaymentRefundTransfer->getExpressCheckoutUuidOrFail();

        $mollieAmountTransfer = (new MollieAmountTransfer())
            ->setCurrency($mollieExpressCheckoutPaymentRefundTransfer->getCurrencyOrFail())
            ->setValue((string)$mollieExpressCheckoutPaymentRefundTransfer->getAmountOrFail());

        $mollieRefundTransfer = (new MollieRefundTransfer())
            ->setTransactionId($mollieExpressCheckoutPaymentRefundTransfer->getTransactionIdOrFail())
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
