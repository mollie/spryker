<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Mapper;

use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodTransfer;

class MollieClientMapper implements MollieClientMapperInterface
{
    protected const string METHODS_WRAPPER_KEY = '_embedded';

    protected const string METHODS_KEY = 'methods';

    /**
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer
     */
    public function mapPaymentMethodApiResponse(MollieApiResponseTransfer $mollieApiResponseTransfer): MolliePaymentMethodsApiResponseTransfer
    {
        $molliePaymentMethodsApiResponseTransfer = new MolliePaymentMethodsApiResponseTransfer();
        $molliePaymentMethodsApiResponseTransfer
            ->setIsSuccessful($mollieApiResponseTransfer->getIsSuccessful())
            ->setMessage($mollieApiResponseTransfer->getMessage());

        $molliePaymentMethodCollectionTransfer = new MolliePaymentMethodCollectionTransfer();
        $methods = $mollieApiResponseTransfer->getPayload()[static::METHODS_WRAPPER_KEY][static::METHODS_KEY] ?? [];
        foreach ($methods as $method) {
            $molliePaymentMethodTransfer = new MolliePaymentMethodTransfer();

            $molliePaymentMethodTransfer->fromArray($method, true);

            $molliePaymentMethodCollectionTransfer->addMethods($molliePaymentMethodTransfer);
        }

        $molliePaymentMethodsApiResponseTransfer->setCollection($molliePaymentMethodCollectionTransfer);

        return $molliePaymentMethodsApiResponseTransfer;
    }
}
