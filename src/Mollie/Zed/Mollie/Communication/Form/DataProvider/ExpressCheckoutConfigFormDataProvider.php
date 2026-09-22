<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Form\DataProvider;

use Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer;
use Mollie\Zed\Mollie\Business\MollieFacadeInterface;
use Mollie\Zed\Mollie\Communication\Form\ExpressCheckoutConfigForm;

class ExpressCheckoutConfigFormDataProvider
{
    /**
     * @param \Mollie\Zed\Mollie\Business\MollieFacadeInterface $mollieFacade
     */
    public function __construct(
        private MollieFacadeInterface $mollieFacade,
    ) {
    }

    /**
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer
     */
    public function getExpressCheckoutConfigCollection(): MollieExpressCheckoutConfigCollectionTransfer
    {
        return $this->mollieFacade->getExpressCheckoutConfigCollection();
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer $mollieExpressCheckoutConfigCollectionTransfer
     *
     * @return array<string, bool>
     */
    public function getData(MollieExpressCheckoutConfigCollectionTransfer $mollieExpressCheckoutConfigCollectionTransfer): array
    {
        $data = [];
        foreach ($mollieExpressCheckoutConfigCollectionTransfer->getConfigs() as $mollieExpressCheckoutConfigTransfer) {
            $data[$mollieExpressCheckoutConfigTransfer->getMethod()] = $mollieExpressCheckoutConfigTransfer->getIsEnabled();
        }

        return $data;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer $mollieExpressCheckoutConfigCollectionTransfer
     *
     * @return array<string, mixed>
     */
    public function getOptions(MollieExpressCheckoutConfigCollectionTransfer $mollieExpressCheckoutConfigCollectionTransfer): array
    {
        $expressMethods = [];
        foreach ($mollieExpressCheckoutConfigCollectionTransfer->getConfigs() as $mollieExpressCheckoutConfigTransfer) {
            $expressMethods[] = $mollieExpressCheckoutConfigTransfer->getMethod();
        }

        return [ExpressCheckoutConfigForm::OPTION_EXPRESS_METHODS => $expressMethods];
    }
}
