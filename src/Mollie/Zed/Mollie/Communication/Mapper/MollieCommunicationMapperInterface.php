<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Mapper;

use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;

interface MollieCommunicationMapperInterface
{
    /**
     * @param string $locale
     * @param string|null $currencyCode
     *
     * @return \Generated\Shared\Transfer\MollieApiRequestTransfer
     */
    public function createMollieApiRequestTransfer(string $locale, ?string $currencyCode): MollieApiRequestTransfer;

    /**
     * @param string $locale
     * @param \Generated\Shared\Transfer\MollieAmountTransfer|null $amountTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer
     */
    public function createMolliePaymentMethodQueryParametersTransfer(
        string $locale,
        ?MollieAmountTransfer $amountTransfer,
    ): MolliePaymentMethodQueryParametersTransfer;

    /**
     * @param array<string, mixed> $formData
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer|null $configTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer
     */
    public function mapFormDataToPaymentMethodConfigTransfer(
        array $formData,
        ?MolliePaymentMethodConfigTransfer $configTransfer,
    ): MolliePaymentMethodConfigTransfer;

    /**
     * @param string $molliePaymentKey
     * @param string $currencyCode
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer
     */
    public function createMolliePaymentMethodConfigCriteriaTransfer(string $molliePaymentKey, string $currencyCode): MolliePaymentMethodConfigCriteriaTransfer;
}
