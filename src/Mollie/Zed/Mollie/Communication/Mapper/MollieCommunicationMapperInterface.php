<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Mapper;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;

interface MollieCommunicationMapperInterface
{
    /**
     * @param string $locale
     *
     * @return \Generated\Shared\Transfer\MollieApiRequestTransfer
     */
    public function createMollieApiRequestTransfer(string $locale): MollieApiRequestTransfer;

    /**
     * @param string $locale
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer
     */
    public function createMolliePaymentMethodQueryParametersTransfer(string $locale): MolliePaymentMethodQueryParametersTransfer;

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
}
