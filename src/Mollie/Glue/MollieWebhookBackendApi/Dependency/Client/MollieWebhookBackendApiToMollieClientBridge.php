<?php

namespace Mollie\Glue\MollieWebhookBackendApi\Dependency\Client;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\OrderCollectionRequestTransfer;

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
     * @return \Generated\Shared\Transfer\OrderCollectionRequestTransfer
     */
    public function getPaymentById(MollieApiRequestTransfer $mollieApiRequestTransfer): OrderCollectionRequestTransfer
    {
        return $this->mollieClient->getPaymentById($mollieApiRequestTransfer);
    }
}
