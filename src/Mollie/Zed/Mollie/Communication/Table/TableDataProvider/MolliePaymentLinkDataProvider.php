<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Table\TableDataProvider;

use Mollie\Client\Mollie\MollieClientInterface;

class MolliePaymentLinkDataProvider
{
    /**
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     */
    public function __construct(
        protected MollieClientInterface $mollieClient,
    ) {
    }

    /**
     * @return \Generated\Shared\Transfer\MolliePaymentLinkApiResponseTransfer|array<mixed>
     */
    public function getData()
    {
        return [];
    }

    /**
     * @return array<mixed>
     */
    public function getOptions(): array
    {
        return [];
    }
}
