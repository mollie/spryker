<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Mapper;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Mollie\Shared\Mollie\MollieConstants;

class MollieCommunicationMapper implements MollieCommunicationMapperInterface
{
    /**
     * @param string $locale
     *
     * @return \Generated\Shared\Transfer\MollieApiRequestTransfer
     */
    public function createMollieApiRequestTransfer(string $locale): MollieApiRequestTransfer
    {
        return (new MollieApiRequestTransfer())
            ->setMolliePaymentMethodQueryParameters(
                $this->createMolliePaymentMethodQueryParametersTransfer($locale),
            );
    }

        /**
         * @param string $locale
         *
         * @return \Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer
         */
    public function createMolliePaymentMethodQueryParametersTransfer(string $locale): MolliePaymentMethodQueryParametersTransfer
    {
        return (new MolliePaymentMethodQueryParametersTransfer())
            ->setLocale($locale)
            ->setIncludeIssuers(true)
            ->setSequenceType(MollieConstants::MOLLIE_SEQUENCE_TYPE_ONE_OFF);
    }

    /**
     * @param array<string, mixed> $formData
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer|null $configTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer
     */
    public function mapFormDataToPaymentMethodConfigTransfer(
        array $formData,
        ?MolliePaymentMethodConfigTransfer $configTransfer,
    ): MolliePaymentMethodConfigTransfer {
        if (!$configTransfer) {
            $configTransfer = new MolliePaymentMethodConfigTransfer();
        }

        $configTransfer
            ->setIsActive($formData['isActive'])
            ->setStatus($formData['isActive'] ? 'activated' : 'not activated')
            ->setIsLogoVisible($formData['isLogoVisible'])
            ->setMollieId($formData['mollieId']);

        if ($formData['logo']) {
            $configTransfer->setImage(['size2x' => $formData['logo']]);
        }

        return $configTransfer;
    }
}
