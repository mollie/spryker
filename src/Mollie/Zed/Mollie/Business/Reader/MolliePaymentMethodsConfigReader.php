<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Reader;

use Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer;
use Mollie\Zed\Mollie\Communication\Mapper\MollieCommunicationMapper;
use Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface;

class MolliePaymentMethodsConfigReader implements MolliePaymentMethodsConfigReaderInterface
{
    public function __construct(
        private MollieRepositoryInterface $repository,
    ) {
    }
    
    public function getPaymentMethodConfigCollection(?int $localeId): MolliePaymentMethodConfigCollectionTransfer
    {
        return $this->repository->getPaymentMethodConfigCollection($localeId);

    }

    public function getPaymentMethodConfigByMollieKey(string $key): ?MolliePaymentMethodConfigTransfer
    {
        return $this->repository->getMolliePaymentMethodConfigByMollieKey($key);
    }
}

