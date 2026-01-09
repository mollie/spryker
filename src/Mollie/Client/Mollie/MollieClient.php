<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieAvailablePaymentMethodsApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentApiResponseTransfer;
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
     * @return \Generated\Shared\Transfer\MollieAvailablePaymentMethodsApiResponseTransfer
     */
    public function getAvailablePaymentMethods(): MollieAvailablePaymentMethodsApiResponseTransfer
    {
          /** @var \Generated\Shared\Transfer\MollieAvailablePaymentMethodsApiResponseTransfer $mollieAvailablePaymentMethodsApiResponseTransfer */
        $mollieAvailablePaymentMethodsApiResponseTransfer = $this->getFactory()->createAvailablePaymentMethodsApi()->execute();

        return $mollieAvailablePaymentMethodsApiResponseTransfer;
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
}
