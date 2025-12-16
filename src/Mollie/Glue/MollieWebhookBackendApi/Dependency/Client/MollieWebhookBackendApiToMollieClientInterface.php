<?php

namespace Mollie\Glue\MollieWebhookBackendApi\Dependency\Client;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\OrderCollectionRequestTransfer;

interface MollieWebhookBackendApiToMollieClientInterface
{
    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\OrderCollectionRequestTransfer
     */
    public function getPaymentById(MollieApiRequestTransfer $mollieApiRequestTransfer): OrderCollectionRequestTransfer;
}
