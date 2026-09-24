<?php


declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout;

use Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutConfigCriteriaTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutConfigTransfer;
use Mollie\Zed\Mollie\MollieConfig;
use Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface;

class ExpressCheckoutConfigReader implements ExpressCheckoutConfigReaderInterface
{
    /**
     * @param \Mollie\Zed\Mollie\MollieConfig $config
     * @param \Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface $repository
     */
    public function __construct(
        protected MollieConfig $config,
        protected MollieRepositoryInterface $repository,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutConfigCriteriaTransfer $criteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer
     */
    public function getExpressCheckoutConfigCollection(
        MollieExpressCheckoutConfigCriteriaTransfer $criteriaTransfer,
    ): MollieExpressCheckoutConfigCollectionTransfer {
        $expressMethods = $this->config->getExpressMethods();

        if ($criteriaTransfer->getExpressMethod()) {
            $expressMethods = array_intersect($expressMethods, [$criteriaTransfer->getExpressMethod()]);
        }

        $collectionTransfer = new MollieExpressCheckoutConfigCollectionTransfer();
        foreach ($expressMethods as $expressMethod) {
            $isEnabled = $this->isExpressMethodEnabled($expressMethod);

            $expressCheckoutConfigTransfer = new MollieExpressCheckoutConfigTransfer();
            $expressCheckoutConfigTransfer->setMethod($expressMethod);
            $expressCheckoutConfigTransfer->setIsEnabled($isEnabled);

            $collectionTransfer->addConfig($expressCheckoutConfigTransfer);
        }

        return $collectionTransfer;
    }

    /**
     * @param string $expressMethod
     *
     * @return bool
     */
    protected function isExpressMethodEnabled(string $expressMethod): bool
    {
        $methodCriteriaTransfer = (new MollieExpressCheckoutConfigCriteriaTransfer())->setExpressMethod($expressMethod);
        $persistentConfig = $this->repository->getPersistentExpressCheckoutMethodConfig($methodCriteriaTransfer);
        $defaultConfig = $this->config->getDefaultExpressCheckoutMethodConfig();

        if (array_key_exists($expressMethod, $persistentConfig)) {
            return $persistentConfig[$expressMethod];
        }

        if (array_key_exists($expressMethod, $defaultConfig)) {
            return $defaultConfig[$expressMethod];
        }

        return false;
    }
}
