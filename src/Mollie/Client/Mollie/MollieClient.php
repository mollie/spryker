<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer;
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
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer
     */
    public function getEnabledPaymentMethods(MollieApiRequestTransfer $mollieApiRequestTransfer): MolliePaymentMethodsApiResponseTransfer
    {
          /** @var \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer $molliePaymentMethodsApiResponseTransfer */
        $molliePaymentMethodsApiResponseTransfer = $this->getFactory()->createEnabledPaymentMethodsProvider()->provide($mollieApiRequestTransfer);

        return $molliePaymentMethodsApiResponseTransfer;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer
     */
    public function getAllPaymentMethods(MollieApiRequestTransfer $mollieApiRequestTransfer): MolliePaymentMethodsApiResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer $molliePaymentMethodsApiResponseTransfer */
        $molliePaymentMethodsApiResponseTransfer = $this->getFactory()->createAllPaymentMethodsProvider()->provide($mollieApiRequestTransfer);

        return $molliePaymentMethodsApiResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentApiResponseTransfer
     */
    public function getPaymentByTransactionId(MollieApiRequestTransfer $mollieApiRequestTransfer): MolliePaymentApiResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\MolliePaymentApiResponseTransfer $molliePaymentApiResponseTransfer */
        $molliePaymentApiResponseTransfer = $this->getFactory()->createGetPaymentByTransactionIdApi()->execute($mollieApiRequestTransfer);

        return $molliePaymentApiResponseTransfer;
    }

    /**
     * Specification:
     * - Creates a payment in Mollie system
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentApiResponseTransfer
     */
    public function createPayment(MollieApiRequestTransfer $mollieApiRequestTransfer): MolliePaymentApiResponseTransfer
    {
        return $this->getFactory()->createPaymentApi()->execute($mollieApiRequestTransfer);
    }
}
