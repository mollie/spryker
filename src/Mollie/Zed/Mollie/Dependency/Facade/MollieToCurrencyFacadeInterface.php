<?php
declare(strict_types=1);

namespace Mollie\Zed\Mollie\Dependency\Facade;

use Generated\Shared\Transfer\CurrencyCollectionTransfer;
use Generated\Shared\Transfer\CurrencyCriteriaTransfer;

interface MollieToCurrencyFacadeInterface
{
    /**
     * @param \Generated\Shared\Transfer\CurrencyCriteriaTransfer $currencyCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\CurrencyCollectionTransfer
     */
    public function getCurrencyCollection(CurrencyCriteriaTransfer $currencyCriteriaTransfer): CurrencyCollectionTransfer;
}
