<?php

namespace Mollie\Glue\MollieWebhookBackendApi\Dependency\Facade;

use Generated\Shared\Transfer\OrderCollectionRequestTransfer;
use Generated\Shared\Transfer\OrderCollectionResponseTransfer;

class MollieWebhookBackendApiToMollieFacadeBridge implements MollieWebhookBackendApiToMollieFacadeInterface
{
    /**
     * @var \Mollie\Zed\Mollie\Business\MollieFacadeInterface
     */
    protected $mollieFacade;

    /**
     * @param \Mollie\Zed\Mollie\Business\MollieFacadeInterface $mollieFacade
     */
    public function __construct($mollieFacade)
    {
        $this->mollieFacade = $mollieFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\OrderCollectionResponseTransfer
     */
    public function updateOrderCollection(OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer): OrderCollectionResponseTransfer
    {
        return $this->mollieFacade->updateOrderCollection($updateOrderCollectionRequestTransfer);
    }
}
