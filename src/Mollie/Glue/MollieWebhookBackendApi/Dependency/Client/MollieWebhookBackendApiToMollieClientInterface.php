<?php

namespace Mollie\Glue\MollieWebhookBackendApi\Dependency\Client;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentApiResponseTransfer;

interface MollieWebhookBackendApiToMollieClientInterface
{
    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentApiResponseTransfer
     */
    public function getPaymentByTransactionId(MollieApiRequestTransfer $mollieApiRequestTransfer): MolliePaymentApiResponseTransfer;
}
