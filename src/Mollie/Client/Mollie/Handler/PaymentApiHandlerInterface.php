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
     * Specification:
     * - Adds the order reference.
     * - Adds the single-click checkout path when the quote has one.
     *
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer|null $quoteTransfer
     *
     * @return array<string>
     */
    public function createPaymentMetadata(CheckoutResponseTransfer $checkoutResponseTransfer, ?QuoteTransfer $quoteTransfer = null): array;

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return array<string, mixed>
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
