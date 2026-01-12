<?php

declare(strict_types=1);

namespace Mollie\Service\Mollie;

use Spryker\Service\Kernel\AbstractServiceFactory;
use Spryker\Shared\Money\Converter\IntegerToDecimalConverter;
use Spryker\Shared\Money\Converter\IntegerToDecimalConverterInterface;

class MollieServiceFactory extends AbstractServiceFactory
{
    /**
     * @return \Spryker\Shared\Money\Converter\IntegerToDecimalConverterInterface
     */
    public function createIntegerToDecimalConverter(): IntegerToDecimalConverterInterface
    {
        return new IntegerToDecimalConverter();
    }
}
