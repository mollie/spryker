<?php

declare(strict_types = 1);

namespace Mollie\Service\Mollie;

use Mollie\Service\Mollie\Money\MollieAmountConverter;
use Mollie\Service\Mollie\Money\MollieAmountConverterInterface;
use Mollie\Service\Mollie\Url\UrlResolver;
use Mollie\Service\Mollie\Url\UrlResolverInterface;
use Spryker\Service\Kernel\AbstractServiceFactory;
use Spryker\Shared\Money\Converter\IntegerToDecimalConverter;
use Spryker\Shared\Money\Converter\IntegerToDecimalConverterInterface;

class MollieServiceFactory extends AbstractServiceFactory
{
    /**
     * @return \Mollie\Service\Mollie\Money\MollieAmountConverterInterface
     */
    public function createMollieAmountConverter(): MollieAmountConverterInterface
    {
        return new MollieAmountConverter(
            $this->createIntegerToDecimalConverter(),
        );
    }

    /**
     * @return \Spryker\Shared\Money\Converter\IntegerToDecimalConverterInterface
     */
    public function createIntegerToDecimalConverter(): IntegerToDecimalConverterInterface
    {
        return new IntegerToDecimalConverter();
    }

    /**
     * @return \Mollie\Service\Mollie\Url\UrlResolverInterface
     */
    public function createUrlReolver(): UrlResolverInterface
    {
        return new UrlResolver();
    }
}
