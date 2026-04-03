<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Mapper;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer;
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
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer
     */
    public function createMolliePaymentMethodQueryParametersTransfer(
        string $locale,
    ): MolliePaymentMethodQueryParametersTransfer;

    /**
     * @param string|null $molliePaymentKey
     * @param string|null $currencyCode
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer
     */
    public function createMolliePaymentMethodConfigCriteriaTransfer(
        ?string $molliePaymentKey,
        ?string $currencyCode,
    ): MolliePaymentMethodConfigCriteriaTransfer;

    /**
     * @param array<\Mollie\Zed\Mollie\Communication\Mapper\MolliePaymentMethodTransfer> $paymentMethodTransfers
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodCollectionTransfer
     */
    public function createMolliePaymentMethodCollection(array $paymentMethodTransfers): MolliePaymentMethodCollectionTransfer;
}
