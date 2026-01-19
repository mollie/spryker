<?php

namespace Mollie\Client\Mollie\Zed;

use Generated\Shared\Transfer\OrderCollectionRequestTransfer;
use Generated\Shared\Transfer\OrderCollectionResponseTransfer;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;

class MollieStub implements MollieStubInterface
{
    /**
     * @param \Spryker\Client\ZedRequest\ZedRequestClientInterface $zedStub
     */
    public function __construct(protected ZedRequestClientInterface $zedStub)
    {
    }

    /**
     * @param \Generated\Shared\Transfer\OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\OrderCollectionResponseTransfer
     */
    public function updateOrderCollection(OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer): OrderCollectionResponseTransfer
    {
        $updateOrderCollectionResponseTransfer = $this->zedStub->call('/mollie/gateway/update-order-collection', $updateOrderCollectionRequestTransfer);

        return $updateOrderCollectionResponseTransfer;
    }
}
