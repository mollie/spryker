<?php

declare(strict_types=1);

namespace Mollie\Service\Mollie;

use Spryker\Service\Kernel\AbstractService;

/**
 * @method \Mollie\Service\Mollie\MollieServiceFactory getFactory()
 */
class MollieService extends AbstractService implements MollieServiceInterface
{
    /**
     * Calls IntegerToDecimalConverter class from shared layer
     *
     * @api
     *
     * @param int $value
     *
     * @return float
     */
    public function convertIntegerToDecimal(int $value): float
    {
        return $this->getFactory()->createIntegerToDecimalConverter()->convert($value);
    }
}
