<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieAvailablePaymentMethodCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
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
     * Specification:
     * - Creates a payment in Mollie system
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentTransfer
     */
    public function createPayment(MollieApiRequestTransfer $mollieApiRequestTransfer): MolliePaymentTransfer
    {
        return $this->getFactory()->createCreatePaymentApi()->execute($mollieApiRequestTransfer);
    }

    /**
     * Specification:
     * - Gets payment from Mollie system
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentTransfer
     */
    public function getPayment(MollieApiRequestTransfer $mollieApiRequestTransfer): MolliePaymentTransfer
    {
        return $this->getFactory()->createGetPaymentApi()->execute($mollieApiRequestTransfer);
    }
}
