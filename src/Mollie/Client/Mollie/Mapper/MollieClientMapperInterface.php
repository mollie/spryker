<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Mapper;

use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer;

interface MollieClientMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer
     */
    public function mapPaymentMethodApiResponse(MollieApiResponseTransfer $mollieApiResponseTransfer): MolliePaymentMethodsApiResponseTransfer;
}
