<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Mapper;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Generated\Shared\Transfer\MollieWebhookEventTransfer;

interface MollieMapperInterface
{
    /**
     * @param string $locale
     * @param string $billingCountry
     *
     * @return \Generated\Shared\Transfer\MollieApiRequestTransfer
     */
    public function createMollieApiRequestTransfer(string $locale, string $billingCountry): MollieApiRequestTransfer;

    /**
     * @param string $locale
     * @param string $billingCountry
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer
     */
    public function createMolliePaymentMethodQueryParametersTransfer(string $locale, string $billingCountry): MolliePaymentMethodQueryParametersTransfer;

    /**
     * @param array<string, mixed> $requestBody
     *
     * @return \Generated\Shared\Transfer\MollieWebhookEventTransfer
     */
    public function mapRequestPayloadToMollieWebhookEventTransfer(array $requestBody): MollieWebhookEventTransfer;
}
