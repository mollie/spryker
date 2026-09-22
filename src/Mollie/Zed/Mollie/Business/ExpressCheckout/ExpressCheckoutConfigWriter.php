<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout;

use Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer;
use Mollie\Zed\Mollie\MollieConfig;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;

class ExpressCheckoutConfigWriter implements ExpressCheckoutConfigWriterInterface
{
    /**
     * @param \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface $entityManager
     * @param \Mollie\Zed\Mollie\MollieConfig $config
     */
    public function __construct(
        protected MollieEntityManagerInterface $entityManager,
        protected MollieConfig $config,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer $mollieExpressCheckoutConfigCollectionTransfer
     *
     * @return void
     */
    public function saveExpressCheckoutConfigCollection(
        MollieExpressCheckoutConfigCollectionTransfer $mollieExpressCheckoutConfigCollectionTransfer,
    ): void {
        $knownExpressMethods = array_keys($this->config->getDefaultExpressCheckoutMethodConfig());

        foreach ($mollieExpressCheckoutConfigCollectionTransfer->getConfigs() as $mollieExpressCheckoutConfigTransfer) {
            $expressMethod = $mollieExpressCheckoutConfigTransfer->getMethod();
            if (!in_array($expressMethod, $knownExpressMethods, true)) {
                continue;
            }

            $this->entityManager->saveExpressCheckoutConfig(
                $expressMethod,
                (bool)$mollieExpressCheckoutConfigTransfer->getIsEnabled(),
            );
        }
    }
}
