<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\SingleClick;

use Mollie\Zed\Mollie\MollieConfig;
use Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface;

class SingleClickConfigReader implements SingleClickConfigReaderInterface
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
     * @return bool
     */
    public function isSingleClickPaymentEnabled(): bool
    {
        $persistentConfig = $this->repository->findSingleClickConfig();

        if ($persistentConfig !== null) {
            return $persistentConfig;
        }

        return $this->config->isSingleClickPaymentEnabledByDefault();
    }
}
