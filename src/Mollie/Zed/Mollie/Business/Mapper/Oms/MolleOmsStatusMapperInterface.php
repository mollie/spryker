<?php

namespace Mollie\Zed\Mollie\Business\Mapper\Oms;

interface MolleOmsStatusMapperInterface
{
    /**
     * @param string $mollieStatus
     *
     * @return string|null
     */
    public function mapMolliePaymentStatusToOmsStatus(string $mollieStatus): string|null;

    /**
     * @param string $mollieStatus
     *
     * @return string|null
     */
    public function mapMollieRefundStatusToOmsStatus(string $mollieStatus): string|null;
}
