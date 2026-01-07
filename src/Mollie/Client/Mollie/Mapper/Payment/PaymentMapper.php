<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Mapper\Payment;

use Generated\Shared\Transfer\MolliePaymentTransfer;
use Mollie\Client\Mollie\Dependency\MollieToUtilEncodingServiceInterface;
use Mollie\Client\Mollie\Mapper\AbstractMollieApiResponseMapper;
use Mollie\Client\Mollie\Mapper\MollieApiResponseMapperInterface;
use Mollie\Client\Mollie\MollieConfig;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

class PaymentMapper extends AbstractMollieApiResponseMapper implements MollieApiResponseMapperInterface
{
    /**
     * @param \Mollie\Client\Mollie\Dependency\MollieToUtilEncodingServiceInterface $utilEncodingService
     */
    public function __construct(protected MollieToUtilEncodingServiceInterface $utilEncodingService)
    {
    }

    /**
     * @param string $payload
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    public function mapPayloadToResponseTransfer(string $payload): AbstractTransfer
    {
        $molliePaymentTransfer = new MolliePaymentTransfer();
        $payloadArray = $this->utilEncodingService->decodeJson($payload, true);
        $molliePaymentTransfer->fromArray($payloadArray, true);
        $molliePaymentTransfer
            ->setLinks($payloadArray[MollieConfig::RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS] ?? null)
            ->setEmbedded($payloadArray[MollieConfig::RESPONSE_PARAMETER_CREATE_PAYMENT_EMBEDDED] ?? null);

        return $molliePaymentTransfer;
    }
}
