<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Mapper;

use Generated\Shared\Transfer\MollieAvailablePaymentMethodCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodTransfer;
use Mollie\Api\Resources\Method;
use Mollie\Api\Resources\MethodCollection;

interface ApiResponseMapperInterface
{
 /**
  * @param \Mollie\Api\Resources\MethodCollection $methodCollection
  *
  * @return \Generated\Shared\Transfer\MollieAvailablePaymentMethodCollectionTransfer
  */
    public function mapMolliePaymentMethodsToMolliePaymentMethodTransfer(MethodCollection $methodCollection): MollieAvailablePaymentMethodCollectionTransfer;

     /**
      * @param \Mollie\Api\Resources\Method $method
      *
      * @return \Generated\Shared\Transfer\MolliePaymentMethodTransfer
      */
    public function mapMolliePaymentMethodToMolliePaymentMethodTransfer(Method $method): MolliePaymentMethodTransfer;
}
