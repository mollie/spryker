<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie;

use Generated\Shared\Transfer\MollieAvailablePaymentMethodCollectionTransfer;
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
}
