<?php

declare(strict_types=1);

namespace Mollie\Service\Mollie\PaymentLink;

use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Mollie\Service\Mollie\MollieConfig;

class PaymentLinkHandler implements PaymentLinkHandlerInterface
{
    /**
     * @param \Mollie\Service\Mollie\MollieConfig $config
     */
    public function __construct(protected MollieConfig $config)
    {
    }

    /**
     * @return string
     */
    public function getPaymentLinkDefaultExpirationDateTime(): string
    {
        $timezone = new DateTimeZone(date_default_timezone_get());
        $defaultExpirationTime = $this->config->getPaymentLinkDefaultExpirationTime();
        $intervalString = 'PT' . $defaultExpirationTime . 'S';
        $expirationDateTime = (new DateTimeImmutable('now', $timezone))
            ->add(new DateInterval($intervalString));

        return $expirationDateTime->format('Y-m-d\TH:i:sP');
    }
}
