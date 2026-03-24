<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\Payment\RequestSender;

interface MollieReleaseAuthorizationRequestSenderInterface
{
    /**
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function releaseAuthorization(int $idSalesOrder): void;
}
