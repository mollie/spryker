<?php

namespace MollieTest\Zed\Mollie\Helper;

use Codeception\Module;

class MollieFacadeHelper extends Module
{
    /**
     * @param string $apiKey
     *
     * @return bool
     */
    public function checkIfMollieApiKeyExists(string|null $apiKey): bool
    {
        if (!$apiKey) {
            return false;
        }

        return true;
    }
}
