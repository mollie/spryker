<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Handler;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Api\Http\Data\Address;
use Mollie\Api\Http\Data\DataCollection;

interface PaymentApiHandlerInterface
{
    /**
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return array<string>
     */
    public function createPaymentMetadata(CheckoutResponseTransfer $checkoutResponseTransfer): array;

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return array<string, string>
     */
    public function createAdditionalParameters(MollieApiRequestTransfer $mollieApiRequestTransfer): array;

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Mollie\Api\Http\Data\Address
     */
    public function createBillingAddress(QuoteTransfer $quoteTransfer): Address;

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Mollie\Api\Http\Data\DataCollection<array<mixed>>
     */
    public function createLines(QuoteTransfer $quoteTransfer): DataCollection;
}
