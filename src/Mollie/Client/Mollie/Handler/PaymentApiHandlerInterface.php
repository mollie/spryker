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

    /**
     * Specification:
     * - Returns the Mollie customer ID of the quote's customer for a credit card payment with a saved card or with consent to save the card.
     * - Returns null for any other payment.
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return string|null
     */
    public function findMollieCustomerId(QuoteTransfer $quoteTransfer): ?string;

    /**
     * Specification:
     * - Returns the selected saved card (mandate) ID for a credit card payment.
     * - Returns null for any other payment method.
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return string|null
     */
    public function findMandateId(QuoteTransfer $quoteTransfer): ?string;
}
