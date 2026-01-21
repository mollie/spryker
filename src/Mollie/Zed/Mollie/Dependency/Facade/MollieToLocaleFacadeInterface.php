<?php

namespace Mollie\Zed\Mollie\Dependency\Facade;

use Generated\Shared\Transfer\LocaleTransfer;

interface MollieToLocaleFacadeInterface
{
 /**
  * @return \Generated\Shared\Transfer\LocaleTransfer
  */
    public function getCurrentLocale(): LocaleTransfer;
}
