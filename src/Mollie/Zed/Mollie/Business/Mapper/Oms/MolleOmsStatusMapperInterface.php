<?php

namespace Mollie\Zed\Mollie\Business\Mapper\Oms;

interface MolleOmsStatusMapperInterface
{
    /**
     * @param string $mollieStatus
     *
     * @return string|null
     */
    public function mapMollieStatusToOmsStatus(string $mollieStatus): string|null;
}
