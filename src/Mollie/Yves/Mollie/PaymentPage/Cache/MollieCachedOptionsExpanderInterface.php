<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\PaymentPage\Cache;

use Generated\Shared\Transfer\QuoteTransfer;

interface MollieCachedOptionsExpanderInterface
{
    /**
     * @param string $paymentMethod
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    public function expandOptions(string $paymentMethod, QuoteTransfer $quoteTransfer, array $options): array;
}
