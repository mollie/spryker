<?php

namespace Mollie\Glue\MollieWebhookBackendApi\Dependency\Client;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentApiResponseTransfer;

class MollieWebhookBackendApiToMollieClientBridge implements MollieWebhookBackendApiToMollieClientInterface
{
    /**
     * @var \Mollie\Client\Mollie\MollieClientInterface
     */
    protected $mollieClient;

    /**
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     */
    public function __construct($mollieClient)
    {
        $this->mollieClient = $mollieClient;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentApiResponseTransfer
     */
    public function getPaymentByTransactionId(MollieApiRequestTransfer $mollieApiRequestTransfer): MolliePaymentApiResponseTransfer
    {
        return $this->mollieClient->getPaymentByTransactionId($mollieApiRequestTransfer);
    }
}
