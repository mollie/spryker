<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie\Dependency\Client;

use Generated\Shared\Transfer\StoreTransfer;

interface MollieToStoreClientInterface
{
 /**
  * @return \Generated\Shared\Transfer\StoreTransfer
  */
    public function getCurrentStore(): StoreTransfer;
}
