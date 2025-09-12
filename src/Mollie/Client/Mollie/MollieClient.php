<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie;

use Generated\Shared\Transfer\MollieAvailablePaymentMethodCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodTransfer;
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
        return $this->getFactory()->createAvailablePaymentMethodsApi()->getAvailablePaymentMethods();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodTransfer
     */
    public function getIdealPaymentMethod(): MolliePaymentMethodTransfer
    {
        return $this->getFactory()->createAvailablePaymentMethodsApi()->getidealPaymentMethod();
    }
}
