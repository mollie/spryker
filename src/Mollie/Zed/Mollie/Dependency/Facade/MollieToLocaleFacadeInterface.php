<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Dependency\Facade;

use Generated\Shared\Transfer\LocaleTransfer;

interface MollieToLocaleFacadeInterface
{
 /**
  * @return \Generated\Shared\Transfer\LocaleTransfer
  */
    public function getCurrentLocale(): LocaleTransfer;

    /**
     * Specification:
     * - Reads persisted locale by given locale id.
     * - Returns a LocaleTransfer if it's found, throws exception otherwise.
     *
     * @api
     *
     * @param int $idLocale
     *
     * @throws \Spryker\Zed\Locale\Business\Exception\MissingLocaleException
     *
     * @return \Generated\Shared\Transfer\LocaleTransfer
     */
    public function getLocaleById(int $idLocale): LocaleTransfer;
}
