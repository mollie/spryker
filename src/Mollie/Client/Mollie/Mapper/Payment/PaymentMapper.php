<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Mapper\Payment;

use Generated\Shared\Transfer\MolliePaymentTransfer;
use Mollie\Client\Mollie\Mapper\AbstractMollieApiResponseMapper;
use Mollie\Client\Mollie\Mapper\MollieApiResponseMapperInterface;
use Mollie\Client\Mollie\MollieConfig;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

class PaymentMapper extends AbstractMollieApiResponseMapper implements MollieApiResponseMapperInterface
{
    /**
     * @param array<string, mixed> $payload
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    public function mapPayloadToResponseTransfer(array $payload): AbstractTransfer
    {
        $molliePaymentTransfer = new MolliePaymentTransfer();
        $molliePaymentTransfer->fromArray($payload, true);
        $molliePaymentTransfer
            ->setLinks($payload[MollieConfig::RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS] ?? null)
            ->setEmbedded($payload[MollieConfig::RESPONSE_PARAMETER_CREATE_PAYMENT_EMBEDDED] ?? null);

        return $molliePaymentTransfer;
    }
}
