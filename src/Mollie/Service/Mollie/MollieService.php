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

    /**
     * @param string $username
     * @param string $password
     * @param string $webhookUrl
     *
     * @return string
     */
    public function resolveWebhookUrl(string $username, string $password, string $webhookUrl): string
    {
        return $this->getFactory()->createUrlReolver()->resolveWebhookUrl($username, $password, $webhookUrl);
    }
}
