<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Mapper;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieCacheOptionsTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface MollieMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\MollieCacheOptionsTransfer $mollieCacheOptionsTransfer
     *
     * @return \Generated\Shared\Transfer\MollieApiRequestTransfer
     */
    public function createMollieApiRequestTransfer(MollieCacheOptionsTransfer $mollieCacheOptionsTransfer): MollieApiRequestTransfer;

    /**
     * @param \Generated\Shared\Transfer\MollieCacheOptionsTransfer $mollieCacheOptionsTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer
     */
    public function createMolliePaymentMethodQueryParametersTransfer(
        MollieCacheOptionsTransfer $mollieCacheOptionsTransfer,
    ): MolliePaymentMethodQueryParametersTransfer;

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\MollieCacheOptionsTransfer
     */
    public function createMollieCacheOptionsTransfer(QuoteTransfer $quoteTransfer): MollieCacheOptionsTransfer;
}
