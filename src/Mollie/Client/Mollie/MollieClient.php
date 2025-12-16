<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieAvailablePaymentMethodCollectionTransfer;
use Generated\Shared\Transfer\OrderCollectionRequestTransfer;
use Spryker\Client\Kernel\AbstractClient;

/**
 * @method \Mollie\Client\Mollie\MollieFactory getFactory()
 */
class MollieClient extends AbstractClient implements MollieClientInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return \Generated\Shared\Transfer\MollieAvailablePaymentMethodCollectionTransfer
     */
    public function getAvailablePaymentMethods(): MollieAvailablePaymentMethodCollectionTransfer
    {
          /** @var \Generated\Shared\Transfer\MollieAvailablePaymentMethodCollectionTransfer $mollieAvailablePaymentMethodCollectionTransfer */
        $mollieAvailablePaymentMethodCollectionTransfer = $this->getFactory()->createAvailablePaymentMethodsApi()->execute();

        return $mollieAvailablePaymentMethodCollectionTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\OrderCollectionRequestTransfer
     */
    public function getPaymentById(MollieApiRequestTransfer $mollieApiRequestTransfer): OrderCollectionRequestTransfer
    {
        /** @var \Generated\Shared\Transfer\OrderCollectionRequestTransfer $updateOrderCollectionTransfer */
        $updateOrderCollectionTransfer = $this->getFactory()->createGetPaymentByIdApi()->execute($mollieApiRequestTransfer);

        return $updateOrderCollectionTransfer;
    }
}
