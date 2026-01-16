<?php

namespace Mollie\Client\Mollie;

use Mollie\Service\Mollie\MollieServiceInterface;

class TestUrlBuilder implements TestUrlBuilderInterface
{
    /**
     * @param \Mollie\Service\Mollie\MollieServiceInterface $mollieService
     * @param \Mollie\Client\Mollie\MollieConfig $mollieConfig
     */
    public function __construct(
        protected MollieServiceInterface $mollieService,
        protected MollieConfig $mollieConfig,
    ) {
    }

    /**
     * @return string
     */
    public function buildWebhookUrl(): string
    {
        $webhookUrl = $this->mollieService->resolveWebhookUrl(
            $this->mollieConfig->getMollieHtaccessUsername(),
            $this->mollieConfig->getMollieHtaccessPassword(),
            $this->mollieConfig->getMollieWebhookUrl(),
        );

        return $webhookUrl;
    }
}
