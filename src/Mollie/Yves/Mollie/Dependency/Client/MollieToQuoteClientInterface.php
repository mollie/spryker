<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Dependency\Client;

use Generated\Shared\Transfer\QuoteTransfer;

interface MollieToQuoteClientInterface
{
    /**
     * Specification:
     * - Returns the stored quote from session.
     * - Executes stack of {@link \Spryker\Client\QuoteExtension\Dependency\Plugin\DatabaseStrategyReaderPluginInterface} plugins.
     *
     * @api
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function getQuote(): QuoteTransfer;

    /**
     * Specification:
     * - Set quote in session.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    public function setQuote(QuoteTransfer $quoteTransfer): void;

    /**
     * Specification:
     * - Empty existing quote and store to session.
     * - In case of persistent strategy the quote is also deleted from database.
     *
     * @api
     *
     * @return void
     */
    public function clearQuote(): void;

    /**
     * Specification:
     * - Get quote storage strategy type.
     *
     * @api
     *
     * @return string
     */
    public function getStorageStrategy(): string;

    /**
     * Specification:
     * - Returns true if quote is locked.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    public function isQuoteLocked(QuoteTransfer $quoteTransfer): bool;

    /**
     * Specification:
     * - Returns false if quote locked.
     * - Returns true if quote has empty id.
     * - Returns true if customer has `WriteSharedCartPermission`.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    public function isQuoteEditable(QuoteTransfer $quoteTransfer): bool;

    /**
     * Specification:
     *  - Locks quote by setting `isLocked` transfer property to true.
     *  - Low level Quote locking (use CartClientInterface for features).
     *
     * @api
     *
     * @see CartClientInterface::lockQuote()
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function lockQuote(QuoteTransfer $quoteTransfer): QuoteTransfer;
}
