<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\SingleClick;

use Generated\Shared\Transfer\MollieSavedCardRemoveRequestTransfer;
use Generated\Shared\Transfer\MollieSavedCardRemoveResponseTransfer;

interface SavedCardRemoverInterface
{
    /**
     * Specification:
     * - Requires `MollieSavedCardRemoveRequestTransfer.idCustomer` and `MollieSavedCardRemoveRequestTransfer.mandateId`.
     * - Fails when the customer has no Mollie customer or the mandate is not one of the customer's saved cards.
     * - Revokes the mandate at Mollie otherwise.
     *
     * @param \Generated\Shared\Transfer\MollieSavedCardRemoveRequestTransfer $mollieSavedCardRemoveRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieSavedCardRemoveResponseTransfer
     */
    public function removeSavedCard(
        MollieSavedCardRemoveRequestTransfer $mollieSavedCardRemoveRequestTransfer,
    ): MollieSavedCardRemoveResponseTransfer;
}
